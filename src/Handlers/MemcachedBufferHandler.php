<?php

namespace PrivateDev\Monolog\Handlers\MemcachedBufferHandler;

use Monolog\Logger;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;

/**
 * Buffer exception records to Memcached, GROUP them by message and throw every {interval} second as ONE exception
 *
 * Class MemcachedBufferHandler
 *
 * @package App\Components\Monolog\MemcachedBufferHandler
 */
class MemcachedBufferHandler extends AbstractHandler
{
    protected $handler;
    protected $memcached;
    protected $interval;
    protected $bufferLimit;

    /**
     * MemcachedBufferHandler constructor.
     *
     * @param HandlerInterface $handler
     * @param \Memcached       $memcachedDriver You may pass your own cache drive if it implements [add(), get(), set()]
     * @param int              $interval        How often messages will be thrown. In seconds
     * @param int              $level
     * @param bool|true        $bubble
     */
    public function __construct(
        HandlerInterface $handler,
        $memcachedDriver,
        $interval,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        $this->handler   = $handler;
        $this->memcached = $memcachedDriver;
        $this->interval  = $interval;

        parent::__construct($level, $bubble);
    }

    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return false;
        }

        $cacheKey = $this->storeException($record);

        if ($cacheKey) {
            $this->throwGroupedException($cacheKey);
        } else {
            $this->handler->handle($record);
        }
    }

    /**
     * Store exception to Cache
     * Return false if already stored
     *
     * @param $record
     * @return bool
     */
    private function storeException($record)
    {
        $cacheKey = $this->generateCacheKey($record['message']);

        $added = $this->memcached->add(
            $cacheKey,
            [
                'record'         => $record,
                'stored_date'    => time(),
                'message_count'  => 0,
                'interval_count' => $this->interval,
            ],
            $this->interval
        );

        return $added ? false : $cacheKey;
    }

    /**
     * Throws ONE exception per INTERVAL
     *
     * @param $cacheKey
     */
    private function throwGroupedException($cacheKey)
    {
        $storedRecord = $this->memcached->get($cacheKey);
        $storedRecord['message_count']++;
        $waitingTime = time() - $storedRecord['stored_date'];

        if ($waitingTime >= $this->interval) {
            $this->handleStored($storedRecord);
            $storedRecord = $this->updateStoredRecord($storedRecord);
        }

        $this->memcached->set($cacheKey, $storedRecord, $this->interval);
    }

    /**
     * Prepare message for handling record and handle
     *
     * @param $storedRecord
     */
    private function handleStored($storedRecord)
    {
        $storedRecord['record']['message'] = sprintf(
            "It was thrown %d times per %d sec.\n %s",
            $storedRecord['message_count'],
            $storedRecord['interval_count'],
            $storedRecord['record']['message']
        );

        $this->handler->handle($storedRecord['record']);
    }

    /**
     * Update stored record for next iteration
     *
     * @param $storedRecord
     * @return mixed
     */
    private function updateStoredRecord($storedRecord)
    {
        $storedRecord['interval_count'] += $this->interval;
        $storedRecord['stored_date'] = time();

        return $storedRecord;
    }

    /**
     * Generate cache key
     *
     * @param $string
     * @return string
     */
    private function generateCacheKey($string)
    {
        return md5(substr($string, 0, 100));
    }
}

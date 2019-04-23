<?php declare(strict_types=1);

namespace Jad\Map;

/**
 * Interface CacheStorage
 * @package Jad\Map
 */
interface CacheStorage
{
    /**
     * Test if an item exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasItem($key);

    /**
     * Get an item.
     *
     * @param string $key
     * @param bool $success
     * @param mixed $casToken
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null);

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function setItem($key, $value);
}

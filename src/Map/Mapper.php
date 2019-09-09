<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface Mapper
 * @package Jad\Map
 */
interface Mapper
{
    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface;

    /**
     * @return DocumentManager
     */
    public function getDm(): DocumentManager;

    /**
     * @param string $type
     * @return MapItem|null
     */
    public function getMapItem(string $type): ?MapItem;

    /**
     * @param string $type
     * @return bool
     */
    public function hasMapItem(string $type): bool;

    /**
     * @param string $className
     * @return MapItem|null
     */
    public function getMapItemByClass(string $className): ?MapItem;

    /**
     * @return CacheStorage|null
     */
    public function getCache(): ?CacheStorage;

    /**
     * @param CacheStorage|null $cache
     * @return Mapper
     */
    public function setCache(?CacheStorage $cache): Mapper;

    /**
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * @param string $cacheKey
     * @return Mapper
     */
    public function setCacheKey(string $cacheKey): Mapper;
}

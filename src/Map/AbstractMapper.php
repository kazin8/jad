<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ODM\MongoDB\DocumentManager;
use Jad\Exceptions\MappingException;
use Jad\Exceptions\ResourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Jad\Map\MapItem\OrmOdmAssociationMap;

/**
 * Class AbstractMapper
 * @package Jad\Map
 */
abstract class AbstractMapper implements Mapper
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var CacheStorage| null
     */
    private $cache = null;

    /**
     * @var MapItem[]
     */
    protected $map = [];

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * AbstractMapper constructor.
     * @param EntityManagerInterface $em
     * @param CacheStorage|null $cache
     */
    public function __construct(EntityManagerInterface $em, DocumentManager $dm = null, ?CacheStorage $cache = null)
    {
        $this->em = $em;
        $this->dm = $dm;
        $this->cache = $cache;
        $this->cacheKey = static::class;
    }

    /**
     * @codeCoverageIgnore
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return DocumentManager
     */
    public function getDm(): DocumentManager
    {
        return $this->dm;
    }

    /**
     * @codeCoverageIgnore
     * @return MapItem[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param string $type
     * @param $values
     * @param bool $paginate
     * @param OrmOdmAssociationMap[]|null $associationMap
     */
    public function add(string $type, $values, bool $paginate = false, ?array $associationMap = null): void
    {
        $mapItem = new MapItem($type, $values, $paginate, $associationMap);

        $entityClass = $mapItem->getEntityClass();

        try {
            $classMetaData = $this->em->getClassMetadata($entityClass);
        } catch (\Doctrine\Common\Persistence\Mapping\MappingException $exception) {
            $classMetaData = $this->dm->getClassMetadata($entityClass);
        }

        $mapItem->setClassMeta($classMetaData);

        if (!$this->itemExists($mapItem)) {
            $this->map[] = $mapItem;
        }
    }

    /**
     * @param \Jad\Map\MapItem $item
     * @return bool
     */
    public function itemExists(MapItem $item): bool
    {
        /** @var MapItem $mapItem */
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $item->getType()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasMapItem(string $type): bool
    {
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @return MapItem|null
     * @throws ResourceNotFoundException
     */
    public function getMapItem(string $type): ?MapItem
    {
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $type) {
                return $mapItem;
            }
        }

        throw new ResourceNotFoundException('Resource type not found [' . $type . ']');
    }

    /**
     * @param string $className
     * @return MapItem|null
     * @throws MappingException
     */
    public function getMapItemByClass(string $className): ?MapItem
    {
        if (substr($className, 0, 1) !== '\\') {
            $className = '\\' . $className;
        }

        foreach ($this->map as $mapItem) {
            if ($mapItem->getEntityClass() === $className) {
                return $mapItem;
            }
        }

        throw new MappingException('Map item with class name [' . $className . '] not found.', 400);
    }

    /**
     * @return CacheStorage|null
     */
    public function getCache(): ?CacheStorage
    {
        return $this->cache;
    }

    /**
     * @param CacheStorage|null $cache
     * @return AbstractMapper
     */
    public function setCache(?CacheStorage $cache): Mapper
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @param string $cacheKey
     * @return AbstractMapper
     */
    public function setCacheKey(string $cacheKey): Mapper
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }

    protected function reverseAssociations()
    {
        foreach ($this->getMap() as $mapItem) {
            foreach ($mapItem->getOrmOdmAssociationMap() as $ormOdmAssociationMapItem) {
                foreach ($this->map as $associatedKey => $associatedMapItem) {
                    if ($associatedMapItem->getEntityClass() === $ormOdmAssociationMapItem->getDocumentClass()) {
                        $typeName = $ormOdmAssociationMapItem->getTypeName();
                        $associatedTypeName = $ormOdmAssociationMapItem->getAssociatedTypeName();

                        $newMapItem = clone $ormOdmAssociationMapItem;

                        $newMapItem->setAssociatedTypeName($typeName);
                        $newMapItem->setTypeName($associatedTypeName);


                        $associatedMap = $associatedMapItem->getOrmOdmAssociationMap();

                        $found = false;

                        foreach ($associatedMap as $mapItem) {
                            if ($mapItem->getTypeName() == $newMapItem->getTypeName()) {
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $associatedMap[] = $newMapItem;
                            $associatedMapItem->setOrmOdmAssociationMap($associatedMap);
                        }
                    }
                }
            }
        }
    }
}

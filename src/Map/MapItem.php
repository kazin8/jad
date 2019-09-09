<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Jad\Exceptions\JadException;
use Jad\Map\Annotations\Header;
use Doctrine\Common\Annotations\AnnotationReader;
use Jad\Map\MapItem\OrmOdmAssociationMap;

/**
 * Class MapItem
 * @package Jad\Map
 */
class MapItem
{
    /**
     * @var string
     */
    private $type = 'undefined';

    /**
     * @var string
     */
    private $entityClass = '';

    /**
     * @var ClassMetadata $classMeta
     */
    private $classMeta;

    /**
     * @var OrmOdmAssociationMap[]
     */
    private $ormOdmAssociationMap = [];

    /**
     * @var bool
     */
    private $paginate = false;

    /**
     * MapItem constructor.
     * @param string $type
     * @param $params
     * @param bool $paginate
     * @param OrmOdmAssociationMap[]|null $associationMap
     */
    public function __construct(string $type, $params, bool $paginate = false, ?array $associationMap = null)
    {
        $this->setType($type);
        $this->setPaginate($paginate);

        if (!is_null($associationMap)) {
            $this->setOrmOdmAssociationMap($associationMap);
        }

        if (is_string($params)) {
            $this->setEntityClass($params);
        }

        if (is_array($params)) {
            if (array_key_exists('entityClass', $params)) {
                $this->setEntityClass($params['entityClass']);
            }

            if (array_key_exists('classMeta', $params)) {
                $this->setClassMeta($params['classMeta']);
            }
        }
    }

    public function getAssociationMapsKeys(): array
    {
        $result = array_keys($this->getClassMeta()->getAssociationNames());

        foreach ($this->getOrmOdmAssociationMap() as $associationMap) {
            $result[] = $associationMap->getTypeName();
        }

        return array_unique($result);
    }

    public function hasAssociation(string $name)
    {
        return in_array($name, $this->getAssociationMapsKeys());
    }

    /**
     * @return OrmOdmAssociationMap[]
     */
    public function getOrmOdmAssociationMap(): array
    {
        return $this->ormOdmAssociationMap;
    }

    /**
     * @param OrmOdmAssociationMap[] $ormOdmAssociationMap
     * @return MapItem
     */
    public function setOrmOdmAssociationMap(array $ormOdmAssociationMap): MapItem
    {
        $this->ormOdmAssociationMap = $ormOdmAssociationMap;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param string $type
     * @return MapItem
     */
    private function setType(string $type): MapItem
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param string $entityClass
     * @return MapItem
     */
    private function setEntityClass(string $entityClass): MapItem
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     * @throws JadException
     */
    public function getIdField(): string
    {
        if (!$this->classMeta instanceof ClassMetadata) {
            throw new JadException('No class meta data found');
        }

        $identifier = $this->classMeta->getIdentifier();

        if (count($identifier) > 1) {
            throw new JadException('Composite identifier not supported');
        }

        if (count($identifier) < 1) {
            throw new JadException('No identifier found');
        }

        return $identifier[0];
    }

    /**
     * @codeCoverageIgnore
     * @return ClassMetadata
     */
    public function getClassMeta(): ClassMetadata
    {
        return $this->classMeta;
    }

    /**
     * @codeCoverageIgnore
     * @param ClassMetadata $classMeta
     * @return $this
     */
    public function setClassMeta(ClassMetadata $classMeta): MapItem
    {
        $this->classMeta = $classMeta;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function isPaginate(): bool
    {
        return $this->paginate;
    }

    /**
     * @codeCoverageIgnore
     * @param bool $paginate
     */
    public function setPaginate(bool $paginate): void
    {
        $this->paginate = $paginate;
    }

    /**
     * @return bool
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function isReadOnly(): bool
    {
        $reader = new AnnotationReader();
        $reflection = new \ReflectionClass($this->getEntityClass());

        $header = $reader->getClassAnnotation($reflection, Header::class);

        if (!is_null($header)) {
            if (property_exists($header, 'readOnly')) {
                $readOnly = is_null($header->readOnly) ? false : (bool)$header->readOnly;

                if ($readOnly) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}

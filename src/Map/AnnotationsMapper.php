<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jad\Common\Text;
use Jad\Map\Annotations\OrmOdmAssociation;
use Jad\Map\MapItem\OrmOdmAssociationMap;

/**
 * Class AnnotationsMapper
 * @package Jad\Map
 */
class AnnotationsMapper extends AbstractMapper
{

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * AnnotationsMapper constructor.
     * @param EntityManagerInterface $em
     * @param CacheStorage| null $cache
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(EntityManagerInterface $em, DocumentManager $dm = null, ?CacheStorage $cache = null)
    {
        parent::__construct($em, $dm, $cache);

        $cacheKey = $this->getCacheKey();

        if ($cache && $cache->hasItem($cacheKey)) {
            $this->map = $cache->getItem($cacheKey);
        } else {
            $this->annotationReader = new AnnotationReader();

            $this->readMeta($em->getMetadataFactory()->getAllMetadata());
            $this->readMeta($dm->getMetadataFactory()->getAllMetadata());
            $this->reverseAssociations();


            if ($cache) {
                $cache->setItem($cacheKey, $this->map);
            }
        }
    }

    private function readMeta(array $metaData)
    {
        /** @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $meta */
        foreach ($metaData as $meta) {
            $head = $this->annotationReader->getClassAnnotation($meta->getReflectionClass(), Annotations\Header::class);
            $ormOdmAssociationMaps = [];

            if (!empty($head) && !empty($head->type)) {
                foreach ($meta->getReflectionClass()->getProperties() as $reflectionProperty) {
                    $ormOdmAssociation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, OrmOdmAssociation::class);

                    if ($ormOdmAssociation instanceof OrmOdmAssociation) {
                        $ormOdmAssociationMaps[] = new OrmOdmAssociationMap($ormOdmAssociation, $reflectionProperty->getName(), $meta->getReflectionClass()->getName(), $head->type);
                    }
                }

                $className = $meta->getName();
                $paginate = !!$head->paginate;
                $this->add($head->type, $className, $paginate, $ormOdmAssociationMaps);

                if (!empty($head->aliases)) {
                    $aliases = explode(',', $head->aliases);

                    foreach ($aliases as $type) {
                        $this->add($type, $className, $paginate, $ormOdmAssociationMaps);
                    }
                }

                foreach ($ormOdmAssociationMaps as $ormOdmAssociationMap) {
                    $this->add(
                        Text::kebabify($ormOdmAssociationMap->getTypeName()),
                        $ormOdmAssociationMap->getDocumentClass(),
                        $ormOdmAssociationMap->isPaginate()
                    );
                }

                if ($meta instanceof ClassMetadata) {
                    // Set auto aliases for relationship mappings that do not
                    // @phan-suppress-next-line PhanUndeclaredMethod
                    foreach ($meta->getAssociationMappings() as $associatedType => $associatedData) {
                        $targetType = $associatedData['targetEntity'];
                        $targetType = preg_replace('/.*\\\(.+?)/', '$1', $targetType);
                        $associatedType = ucfirst($associatedType);

                        if ($targetType !== $associatedType) {
                            $this->add(
                                Text::kebabify($associatedType),
                                $associatedData['targetEntity'],
                                $paginate
                            );
                        }
                    }
                }
            }
        }
    }
}

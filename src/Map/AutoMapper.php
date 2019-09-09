<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AutoMapper
 * @package Jad\Map
 * @deprecated Use annotation mapper
 */
class AutoMapper extends AbstractMapper
{
    /**
     * AutoMapper constructor.
     * @param EntityManagerInterface $em
     * @param array $excluded
     * @param CacheStorage|null $cache
     */
    public function __construct(EntityManagerInterface $em, DocumentManager $dm = null, array $excluded = [], ?CacheStorage $cache = null)
    {
        parent::__construct($em, $dm, $cache);

        $metaData = $em->getMetadataFactory()->getAllMetadata();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach ($metaData as $meta) {
            $className = $meta->getName();

            if (preg_match('/^.*\\\(.+?)$/', $className, $matches) && !empty($matches[1])) {
                $type = strtolower($matches[1]);

                if (!in_array($type, $excluded)) {
                    $this->add($type, $className);
                }
            }
        }
    }
}

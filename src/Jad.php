<?php

namespace Jad;

use Jad\Map\Mapper;
use Doctrine\ORM\EntityManager;
use Tobscure\JsonApi\Document;

class Jad
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var Mapper $entityMap
     */
    private $entityMap;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * Jad constructor.
     * @param EntityManager $em
     * @param Mapper $entityMap
     */
    public function __construct(EntityManager $em, Mapper $entityMap)
    {
        $this->em = $em;
        $this->entityMap = $entityMap;
        $this->requestHandler = new RequestHandler();
    }

    /**
     * @return \Jad\RequestHandler
     */
    public function getRequestHandler(): RequestHandler
    {
        return $this->requestHandler;
    }

    /**
     * @param $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->getRequestHandler()->setPathPrefix($pathPrefix);
    }

    public function jsonApiResult()
    {
        $type = $this->requestHandler->getType();
        $mapItem = $this->entityMap->getEntityMapItem($type);

        $dh = new DoctrineHandler($mapItem, $this->em, $this->requestHandler);

        if($this->requestHandler->hasId()) {
            $resource = $dh->getEntityById($this->requestHandler->getId());
            $document = new Document($resource);
        } else {
            $collection = $dh->getEntities();
            $document = new Document($collection);
        }

        $document->addLink('self', $this->getUrl());

        return json_encode($document);
    }

    private function getUrl()
    {
        return $this->requestHandler->getRequest()->getSchemeAndHttpHost()
            . $this->requestHandler->getRequest()->getPathInfo();
    }
}
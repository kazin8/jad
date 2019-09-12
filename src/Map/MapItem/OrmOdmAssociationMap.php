<?php declare(strict_types=1);

namespace Jad\Map\MapItem;

use Jad\Map\Annotations\OrmOdmAssociation;

class OrmOdmAssociationMap
{
    /**
     * @var string
     */
    private $documentClass;

    /**
     * @var string
     */
    private $documentField;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $entityField;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var string
     */
    private  $associatedTypeName;

    /**
     * @var bool
     */
    private $paginate = true;

    /**
     * OrmOdmAssociationMap constructor.
     * @param $documentClass
     */
    public function __construct(OrmOdmAssociation $association, string $typeName,string $entityClass, string $associatedTypeName)
    {
        $this->documentClass = $association->documentClass;
        $this->documentField = $association->documentField;
        $this->entityClass = $entityClass;
        $this->entityField = $association->entityField;
        $this->paginate = $association->paginate;
        $this->typeName = $typeName;
        $this->associatedTypeName = $associatedTypeName;
    }


    /**
     * @return mixed
     */
    public function getDocumentClass()
    {
        return $this->documentClass;
    }

    /**
     * @param mixed $documentClass
     * @return OrmOdmAssociationMap
     */
    public function setDocumentClass($documentClass)
    {
        $this->documentClass = $documentClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentField()
    {
        return $this->documentField;
    }

    /**
     * @param mixed $documentField
     * @return OrmOdmAssociationMap
     */
    public function setDocumentField($documentField)
    {
        $this->documentField = $documentField;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return OrmOdmAssociationMap
     */
    public function setEntityClass(string $entityClass): OrmOdmAssociationMap
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityField()
    {
        return $this->entityField;
    }

    /**
     * @param mixed $entityField
     * @return OrmOdmAssociationMap
     */
    public function setEntityField($entityField)
    {
        $this->entityField = $entityField;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaginate(): bool
    {
        return $this->paginate;
    }

    /**
     * @param bool $paginate
     * @return OrmOdmAssociationMap
     */
    public function setPaginate(bool $paginate): OrmOdmAssociationMap
    {
        $this->paginate = $paginate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     * @return OrmOdmAssociationMap
     */
    public function setTypeName(string $typeName): OrmOdmAssociationMap
    {
        $this->typeName = $typeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssociatedTypeName(): string
    {
        return $this->associatedTypeName;
    }

    /**
     * @param string $associatedTypeName
     * @return OrmOdmAssociationMap
     */
    public function setAssociatedTypeName(string $associatedTypeName): OrmOdmAssociationMap
    {
        $this->associatedTypeName = $associatedTypeName;
        return $this;
    }
}

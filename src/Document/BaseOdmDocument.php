<?php declare(strict_types=1);

namespace Jad\Document;

use Jad\Map\AnnotationsMapper;

class BaseOdmDocument
{
    /**
     * @var AnnotationsMapper|null
     */
    private $annotationsMapper;

    /**
     * @return AnnotationsMapper|null
     */
    public function getAnnotationsMapper(): ?AnnotationsMapper
    {
        return $this->annotationsMapper;
    }

    /**
     * @param AnnotationsMapper|null $annotationsMapper
     * @return BaseOdmDocument
     */
    public function setAnnotationsMapper(?AnnotationsMapper $annotationsMapper): BaseOdmDocument
    {
        $this->annotationsMapper = $annotationsMapper;
        return $this;
    }
}

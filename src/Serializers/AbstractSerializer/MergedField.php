<?php declare(strict_types=1);

namespace Jad\Serializers\AbstractSerializer;

class MergedField
{
    /**
     * @var object|null
     */
    private $jadAnnotation = null;

    /**
     * @var object|null
     */
    private $annotation = null;

    /**
     * @return object|null
     */
    public function getJadAnnotation(): ?object
    {
        return $this->jadAnnotation;
    }

    /**
     * @param object|null $jadAnnotation
     * @return MergedField
     */
    public function setJadAnnotation(?object $jadAnnotation): MergedField
    {
        $this->jadAnnotation = $jadAnnotation;
        return $this;
    }

    /**
     * @return object|null
     */
    public function getAnnotation(): ?object
    {
        return $this->annotation;
    }

    /**
     * @param object|null $annotation
     * @return MergedField
     */
    public function setAnnotation(?object $annotation): MergedField
    {
        $this->annotation = $annotation;
        return $this;
    }
}

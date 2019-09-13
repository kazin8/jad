<?php declare(strict_types=1);

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @phan-file-suppress PhanPluginDescriptionlessCommentOnClass
 * @Target({"CLASS"})
 */
final class ApiIdField implements Annotation
{
    /**
     * @var string
     */
    public $fieldName;

}

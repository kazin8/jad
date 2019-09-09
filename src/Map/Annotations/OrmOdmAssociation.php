<?php declare(strict_types=1);

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @phan-file-suppress PhanPluginDescriptionlessCommentOnClass
 * @Target({"PROPERTY"})
 */
final class OrmOdmAssociation implements Annotation
{
    public $documentClass;

    public $documentField;

    public $entityField;

    public $paginate = true;
}

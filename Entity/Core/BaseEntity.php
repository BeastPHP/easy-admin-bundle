<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle\Entity\Core;

use Beast\EasyAdminBundle\Entity\Core\Traits\PrimaryKeyEntity;
use Beast\EasyAdminBundle\Entity\Core\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
abstract class BaseEntity
{
    use PrimaryKeyEntity;
    use TimestampableEntity;
    use SoftDeleteableEntity;
}

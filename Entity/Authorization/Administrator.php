<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle\Entity\Authorization;

use Beast\EasyAdminBundle\Entity\Core\BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Administrator
 *
 * @ORM\Table(name="administrator")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"username"},
 *     message="用户名已存在"
 * )
 */
class Administrator extends BaseUser
{
    public function __construct(Role $role = null)
    {
        $this->role = $role;
    }

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="administrator", fetch="EAGER")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="cascade")
     */
    private $role;

    /**
     * @return Role
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        $result = '暂无';
        if ($this->role) {
            $result = $this->role->getName();
        }
        return $result;
    }
}

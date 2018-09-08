<?php
/*
 * This file is part of the Beast package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Beast\EasyAdminBundle\Entity\Authorization;

use Beast\EasyAdminBundle\Entity\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"name"},
 *     message="名称已存在"
 * )
 */
class Role extends BaseEntity
{
    public function __construct(Administrator $administrator = null)
    {
        $this->administrator = $administrator;
        $this->permission = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Administrator", mappedBy="role")
     */
    private $administrator;

    /**
     * @ORM\ManyToMany(targetEntity="Beast\EasyAdminBundle\Entity\Menus\Menus")
     * @ORM\JoinTable(name="role_menus",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="menus_id", referencedColumnName="id")}
     * )
     */
    private $permission;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * @param mixed $administrator
     */
    public function setAdministrator(Administrator $administrator): void
    {
        $this->administrator = $administrator;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     */
    public function setPermission(?ArrayCollection $permission): void
    {
        $this->permission = $permission;
    }

    public function getPermissionForModal(): array
    {
        $result = array();
        foreach ($this->getPermission() as $item) {
            $key = $item->isParent() ? $item->getName() : $item->getParent()->getName();
            if (!isset($result[$key])) {
                $result[$key] = array();
            }

            $result[$key][$item->getId()] = $item->getName();
        }

        return $result;
    }
}

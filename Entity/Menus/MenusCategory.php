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

namespace Beast\EasyAdminBundle\Entity\Menus;

use Beast\EasyAdminBundle\Entity\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenusCategory
 *
 * @ORM\Table(name="menus_category")
 * @ORM\Entity
 */
class MenusCategory extends BaseEntity
{
    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort;

    /**
     * @ORM\OneToMany(targetEntity="Menus", mappedBy="menusCategory")
     */
    private $menus;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return Collection|Menus[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menus $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->setMenusCategory($this);
        }

        return $this;
    }

    public function removeMenu(Menus $menu): self
    {
        if ($this->menus->contains($menu)) {
            $this->menus->removeElement($menu);
            // set the owning side to null (unless already changed)
            if ($menu->getMenusCategory() === $this) {
                $menu->setMenusCategory(null);
            }
        }

        return $this;
    }
}

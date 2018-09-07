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

use Beast\EasyAdminBundle\Entity\Core\PrimaryKeyTrait;
use Beast\EasyAdminBundle\Entity\Core\TimestampTrait;
use Beast\EasyAdminBundle\Helper\CoreHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Menus
 *
 * @ORM\Table(
 *     name="menus",
 *     indexes={@ORM\Index(columns={"menus_category_id"}), @ORM\Index(columns={"parent_id"})}
 * )
 * @ORM\Entity
 */
class Menus
{
    use PrimaryKeyTrait, TimestampTrait;

    public function __construct(self $menus = null, MenusCategory $category = null)
    {
        $this->menusCategory = $category;
        $this->parent = $menus;
    }

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MenusCategory", inversedBy="menus")
     * @ORM\JoinColumn(name="menus_category_id", referencedColumnName="id", onDelete="cascade")
     */
    private $menusCategory;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Menus")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="cascade")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="active", type="text", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(?string $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getMenusCategory(): ?MenusCategory
    {
        return $this->menusCategory;
    }

    public function setMenusCategory(?MenusCategory $menusCategory): self
    {
        $this->menusCategory = $menusCategory;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getUrlForTwig(): string
    {
        $container = CoreHelper::getCoreKernel()->getContainer();
        try {
            $result = $container->get('router')->generate($this->getUrl());
        } catch (RouteNotFoundException $e) {
            $result = '#';
        }
        return $result;
    }

    public function isParent(): bool
    {
        return $this->getParent() == null;
    }
}

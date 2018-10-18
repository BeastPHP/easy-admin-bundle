<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Repository\Menus;

use Beast\EasyAdminBundle\Entity\Menus\MenusCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class MenusCategoryRepository
 *
 * @package Beast\EasyAdminBundle\Repository\Menus
 */
class MenusCategoryRepository extends ServiceEntityRepository
{
    /**
     * MenusCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MenusCategory::class);
    }
}

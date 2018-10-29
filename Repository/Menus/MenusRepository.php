<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Repository\Menus;

use Beast\EasyAdminBundle\Entity\Menus\Menus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class MenusRepository
 *
 * @package Beast\EasyAdminBundle\Repository\Menus
 */
class MenusRepository extends ServiceEntityRepository
{
    /**
     * MenusRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Menus::class);
    }

    /**
     * @return array
     */
    public function getParentMenus(): array
    {
        $result = [];
        $query = $this->createQueryBuilder('p');
        $result = $query->where(
            $query->expr()->isNull('p.parent')
        )->orderBy('p.sort', 'ASC')->getQuery()->getResult();

        return $result;
    }

    /**
     * @return array
     */
    public function getChildMenus(): array
    {
        $result = [];
        $query = $this->createQueryBuilder('p');
        $result = $query->where(
            $query->expr()->isNotNull('p.parent')
        )->orderBy('p.sort', 'ASC')->getQuery()->getResult();

        return $result;
    }
}

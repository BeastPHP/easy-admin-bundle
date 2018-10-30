<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BaseService
 *
 * @package Beast\EasyAdminBundle\Service
 */
abstract class BaseService
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var ObjectManager|null
     */
    protected $em;

    /**
     * @var null
     */
    protected $entityClass;

    /**
     * BaseService constructor.
     *
     * @param ManagerRegistry $managerRegistry
     * @param PaginatorInterface $paginator
     * @param RequestStack $requestStack
     * @param $entityClass
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        PaginatorInterface $paginator,
        RequestStack $requestStack,
        $entityClass = null
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->paginator = $paginator;
        $this->managerRegistry = $managerRegistry;

        $this->em = $managerRegistry->getManagerForClass($entityClass);
        $this->entityClass = $entityClass;
    }

    /**
     * @param $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository($entityName = null): ObjectRepository
    {
        $entityClass = $this->entityClass;
        if (!is_null($entityName)) {
            $entityClass = $entityName;
        }

        return $this->managerRegistry->getRepository($entityClass);
    }

    /**
     * @param null $name
     *
     * @return ObjectManager
     */
    public function getManager($name = null): ObjectManager
    {
        return $this->managerRegistry->getManager($name);
    }

    /**
     * Gets the object manager associated with a given class.
     *
     * @param string $class A persistent object class name.
     *
     * @return ObjectManager|null
     */
    public function getManagerForClass($class)
    {
        return $this->managerRegistry->getManagerForClass($class);
    }

    /**
     * @param $target
     * @param int $page
     * @param int $limit
     * @param array $options
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate($target, $page = 1, $limit = 10, array $options = array())
    {
        return $this->paginator->paginate($target);
    }
}

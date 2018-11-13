<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Beast\EasyAdminBundle\Helper\Core;

use Beast\EasyAdminBundle\Entity\Core\BaseUser;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait ServiceTrait
 *
 * @package Beast\EasyAdminBundle\Helper\Core
 * @property ContainerInterface $container
 */
trait ServiceTrait
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
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var BaseUser
     */
    protected $user;

    /**
     * @param $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository($entityName = null): ObjectRepository
    {
        if (is_null($entityName)) {
            $entityName = $this->entityClass;
        }

        return $this->managerRegistry->getRepository($entityName);
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

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     *
     * @final
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException(
                'The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".'
            );
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }
}

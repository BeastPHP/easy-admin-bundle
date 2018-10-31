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

namespace Beast\EasyAdminBundle\Service\Administrator;

use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Beast\EasyAdminBundle\Helper\Util;
use Beast\EasyAdminBundle\Service\BaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AdministratorService
 *
 * @package Beast\EasyAdminBundle\Service\Administrator
 */
class AdministratorService extends BaseService
{
    /**
     * AdministratorService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, Administrator::class);
    }

    /**
     *  $query = $this->getRepository(Administrator::class)->createQueryBuilder('a');
     *      ->select('a', 'b')
     *      ->leftJoin('a.role', 'b');//preload role info
     *
     * @return array
     */
    public function getPagination(): array
    {
        $query = $this->repository->createQueryBuilder('a');
        $pagination = $this->paginate($query->getQuery(), $this->request->get('page', 1));

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * @return array
     */
    public function changeActiveStatus(): array
    {
        $id = $this->request->get('id');
        $object = $this->repository->find($id);

        $response = ['status' => Util::ID_INACTIVE];
        if ($object) {
            if ($object->getIsActive() == Util::ID_ACTIVE) {
                $object->setIsActive(Util::ID_INACTIVE);
            } else {
                $object->setIsActive(Util::ID_ACTIVE);
            }

            /* 已经取到了 $object 对象,此处不必调用$em->persist($object) */
            $this->em->flush();

            $response['status'] = Util::ID_ACTIVE;
            $response['data']['is_active'] = $object->getIsActive();
        }

        return $response;
    }
}

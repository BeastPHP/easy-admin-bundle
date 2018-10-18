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

namespace Beast\EasyAdminBundle\Controller\Admin\Authorization;

use Beast\EasyAdminBundle\Controller\Admin\BaseController;
use Beast\EasyAdminBundle\Entity\Authorization\Role;
use Beast\EasyAdminBundle\Form\Role\RoleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends BaseController
{
    /**
     * @Route("/index", name="beast_admin_role_index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $query = $this->getRepository(Role::class)->createQueryBuilder('a')
            ->select('a', 'b')
            ->leftJoin('a.permission', 'b');//preload menus info

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getQuery(), $request->get('page', 1));

        return $this->render(
            '@BeastEasyAdmin/resources/admin/role/index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @Route("edit/{id}", defaults={"id" = NULL}, name="beast_admin_role_edit")
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request): Response
    {
        $parameters = $request->get('role');

        $id = $parameters['id'] ?? $request->get('id');
        $repository = $this->getRepository(Role::class);
        $em = $this->getObjectManager();

        if ($id) {
            $object = $repository->findOneBy(['id' => $id]);
        } else {
            $object = new Role();
        }

        if (!$object) {
            return $this->redirect($this->generateUrl('beast_core_page_for_404', $request->query->all()));
        }

        $form = $this->createForm(RoleType::class, $object);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($object);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', '保存成功');

                return $this->redirect($this->generateUrl('beast_admin_role_index', $request->query->all()));
            }
        }

        return $this->render(
            '@BeastEasyAdmin/resources/admin/role/edit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $object->getId()
            )
        );
    }

    /**
     * @Route("/delete/{ids}", defaults={"ids" = NULL}, name="beast_admin_role_delete")
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        if ($request->get('ids')) {
            $ids = $request->get('ids');
        } else {
            $ids = $request->request->get('ids');
        }

        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $em = $this->getObjectManager();
        $objects = $this->getRepository(Role::class)->findBy(['id' => $ids]);
        foreach ($objects as $object) {
            $em->remove($object);
        }
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '删除成功');

        return $this->redirect($this->generateUrl('beast_admin_role_index', $request->query->all()));
    }
}

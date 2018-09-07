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

namespace Beast\EasyAdminBundle\Controller\Admin\Menus;

use Beast\EasyAdminBundle\Controller\Admin\BaseController;
use Beast\EasyAdminBundle\Entity\Menus\Menus;
use Beast\EasyAdminBundle\Form\Menus\MenusType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MenusController
 *
 * @package App\Controller\Admin\Menus
 */
class MenusController extends BaseController
{
    /**
     * @Route("/index", name="beast_admin_menus_index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $query = $this->getRepository(Menus::class)->createQueryBuilder('a');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getQuery(), $request->get('page', 1));
        return $this->render(
            '@BeastEasyAdmin/resources/admin/menus/index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @Route("/edit/{id}", defaults={"id" = NULL}, name="beast_admin_menus_edit")
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request): Response
    {
        $parameters = $request->get('menus');
        $id = $parameters['id'] ?? $request->get('id');
        $repository = $this->getRepository(Menus::class);
        $em = $this->getObjectManager();

        if ($id) {
            $object = $repository->findOneBy(['id' => $id]);
        } else {
            $object = new Menus();
        }

        if (!$object) {
            return $this->redirect($this->generateUrl('beast_core_page_for_404', $request->query->all()));
        }

        $form = $this->createForm(MenusType::class, $object);
        if ("POST" == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($object);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', '保存成功');

                return $this->redirect($this->generateUrl('beast_admin_menus_index', $request->query->all()));
            }
        }

        return $this->render(
            '@BeastEasyAdmin/resources/admin/menus/edit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $object->getId()
            )
        );
    }

    /**
     * @Route("/delete/{ids}", defaults={"ids" = NULL}, name="beast_admin_menus_delete")
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
        $objects = $this->getRepository(Menus::class)->findBy(['id' => $ids]);
        foreach ($objects as $object) {
            $em->remove($object);
        }
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '删除成功');
        return $this->redirect($this->generateUrl('beast_admin_menus_index', $request->query->all()));
    }
}

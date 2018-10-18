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
use Beast\EasyAdminBundle\Entity\Menus\MenusCategory;
use Beast\EasyAdminBundle\Form\Menus\MenusCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MenusCategoryController
 *
 * @package App\Controller\Admin\Menus
 */
class MenusCategoryController extends BaseController
{
    /**
     * @Route("/index", name="beast_admin_menus_category_index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $query = $this->getRepository(MenusCategory::class)->createQueryBuilder('a');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getQuery(), $request->get('page', 1));
        return $this->render(
            '@BeastEasyAdmin/resources/admin/menusCategory/index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @Route("/edit/{id}", defaults={"id" = NULL}, name="beast_admin_menus_category_edit")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $parameters = $request->get('menus_category');
        $repository = $this->getRepository(MenusCategory::class);
        $id = $parameters['id'] ?? $request->get('id');
        $em = $this->getObjectManager();

        if ($id) {
            $object = $repository->findOneBy(['id' => $id]);
        } else {
            $object = new MenusCategory();
        }

        if (!$object) {
            return $this->redirect($this->generateUrl('beast_core_page_for_404', $request->query->all()));
        }

        $form = $this->createForm(MenusCategoryType::class, $object);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($object);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', '保存成功');

                return $this->redirect($this->generateUrl('beast_admin_menus_category_index', $request->query->all()));
            }
        }

        return $this->render(
            '@BeastEasyAdmin/resources/admin/menusCategory/edit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $object->getId()
            )
        );
    }
}

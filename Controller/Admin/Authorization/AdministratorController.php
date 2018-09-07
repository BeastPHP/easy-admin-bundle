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
use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Beast\EasyAdminBundle\Form\Administrator\AdministratorType;
use Beast\EasyAdminBundle\Helper\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministratorController extends BaseController
{
    /**
     * @Route("/index", name="beast_admin_administrator_index")
     * @param Request $request
     *
     * @return Response
     *  $query = $this->getRepository(Administrator::class)->createQueryBuilder('a');
     *      ->select('a', 'b')
     *      ->leftJoin('a.role', 'b');//preload role info
     */
    public function indexAction(Request $request): Response
    {
        $query = $this->getRepository(Administrator::class)->createQueryBuilder('a');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getQuery(), $request->get('page', 1));
        return $this->render(
            '@BeastEasyAdmin/resources/admin/administrator/index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @Route("edit/{id}", defaults={"id" = NULL}, name="beast_admin_administrator_edit")
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request): Response
    {
        $parameters = $request->get('administrator');

        $id = $parameters['id'] ?? $request->get('id');
        $repository = $this->getRepository(Administrator::class);
        $em = $this->getObjectManager();

        if ($id) {
            $object = $repository->findOneBy(['id' => $id]);
        } else {
            $object = new Administrator();
        }

        if (!$object) {
            return $this->redirect($this->generateUrl('beast_core_page_for_404', $request->query->all()));
        }

        $form = $this->createForm(AdministratorType::class, $object);
        if ("POST" == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($object);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', '保存成功');

                return $this->redirect($this->generateUrl('beast_admin_administrator_index', $request->query->all()));
            }
        }

        return $this->render(
            '@BeastEasyAdmin/resources/admin/administrator/edit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $object->getId()
            )
        );
    }

    /**
     * @Route(
     *     "/change/active/status/{id}",
     *     defaults={"id" = NULL},
     *     name="beast_admin_administrator_change_active_status"
     * )
     * @param Request $request
     * Method(POST)
     *
     * @return JsonResponse
     */
    public function changeActiveStatusAction(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $object = $this->getRepository(Administrator::class)->find($id);

        $response = array('status' => Util::ID_INACTIVE);
        if ($object) {
            if ($object->getIsActive() == Util::ID_ACTIVE) {
                $object->setIsActive(Util::ID_INACTIVE);
            } else {
                $object->setIsActive(Util::ID_ACTIVE);
            }

            $em = $this->getObjectManager();
            $em->persist($object);
            $em->flush();

            $response['status'] = Util::ID_ACTIVE;
            $response['isActive'] = $object->getIsActive();
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/delete/{ids}", defaults={"ids" = NULL}, name="beast_admin_administrator_delete")
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
        $objects = $this->getRepository(Administrator::class)->findBy(['id' => $ids]);
        foreach ($objects as $object) {
            $em->remove($object);
        }
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '删除成功');
        return $this->redirect($this->generateUrl('beast_admin_administrator_index', $request->query->all()));
    }
}

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
use Beast\EasyAdminBundle\Service\Administrator\AdministratorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdministratorController
 *
 * @package Beast\EasyAdminBundle\Controller\Admin\Authorization
 */
class AdministratorController extends BaseController
{
    /**
     * @var AdministratorService
     */
    protected $administratorService;

    /**
     * AdministratorController constructor.
     *
     * @param AdministratorService $administratorService
     */
    public function __construct(AdministratorService $administratorService)
    {
        $this->administratorService = $administratorService;
    }

    /**
     * @Route("/index", name="beast_admin_administrator_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render(
            '@BeastEasyAdmin/resources/admin/administrator/index.html.twig',
            $this->administratorService->getPagination()
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
        if ($request->isMethod('POST')) {
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
            [
                'form' => $form->createView(),
                'id' => $object->getId()
            ]
        );
    }

    /**
     * @Route(
     *     "/change/active/status/{id}",
     *     defaults={"id" = NULL},
     *     name="beast_admin_administrator_change_active_status"
     * )
     * Method(POST)
     *
     * @return JsonResponse
     */
    public function changeActiveStatusAction(): JsonResponse
    {
        return $this->json($this->administratorService->changeActiveStatus());
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

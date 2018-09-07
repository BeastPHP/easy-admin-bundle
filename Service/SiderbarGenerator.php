<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Service;

use Beast\EasyAdminBundle\Helper\CoreHelper;
use Beast\EasyAdminBundle\Repository\Menus\MenusCategoryRepository;
use Beast\EasyAdminBundle\Repository\Menus\MenusRepository;
use Beast\EasyAdminBundle\Controller\Admin\Authorization\AuthorizationController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SiderbarGenerator
{
    protected $token;
    protected $menusRepository;
    protected $menusCategoryRepository;
    protected $user;
    protected $router;

    /**
     * SiderbarGenerator constructor.
     *
     * @param TokenStorageInterface $token
     * @param MenusRepository $menusRepository
     * @param MenusCategoryRepository $menusCategoryRepository
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorageInterface $token,
        MenusRepository $menusRepository,
        MenusCategoryRepository $menusCategoryRepository,
        RouterInterface $router
    ) {
        $this->token = $token->getToken();
        $this->user = $this->token ? $this->token->getUser() : null;
        $this->menusRepository = $menusRepository;
        $this->menusCategoryRepository = $menusCategoryRepository;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function generate(): array
    {
        $result = array();

        if (!$this->user || !$this->user->getRole()) {
            return $result;
        }

        $menusKey = sprintf('role_%s', $this->user->getRole()->getId());
        $permission = $this->user->getRole()->getPermission();

        $redisClient = CoreHelper::getRedisConnection();
        if ($redisClient->hexists('beast_menus', $menusKey)) {
            $result = unserialize($redisClient->hget('beast_menus', $menusKey));
        } else {
            $controllers = $categoryIds = $menusIds = array();
            $controllers[] = AuthorizationController::class;

            foreach ($permission as $item) {
                if ($item->isParent()) {
                    $categoryIds[$item->getMenusCategory()->getId()] = $item->getMenusCategory()->getId();
                } else {
                    if ($route = $this->router->getRouteCollection()->get($item->getUrl())) {
                        $controller = explode('::', $route->getDefaults()['_controller']);
                        if (isset($controller[0])) {
                            $controllers[] = $controller[0];
                        }
                    }
                }

                $menusIds[$item->getId()] = $item->getId();
            }

            $categoryObjects = $this->menusCategoryRepository->findBy(
                [],
                ['sort' => 'ASC']
            );
            $parentMenusObjects = $this->menusRepository->getParentMenus();
            $menusObjects = $this->menusRepository->getChildMenus();

            //顶级分类
            foreach ($categoryObjects as $category) {
                if (isset($categoryIds[$category->getId()])) {
                    $tmp = array();
                    $tmp['name'] = $category->getName();
                    $tmp['child'] = array();
                    $result[$category->getId()] = $tmp;
                }
            }

            //父级分类
            $tmp = array();
            foreach ($parentMenusObjects as $menu) {
                if (isset($menusIds[$menu->getId()])) {
                    $tmp[$menu->getId()]['name'] = $menu->getName();
                    $tmp[$menu->getId()]['category_id'] = $menu->getMenusCategory()->getId();
                    $tmp[$menu->getId()]['icon'] = $menu->getIcon();
                    $tmp[$menu->getId()]['code'] = $menu->getCode();
                    $tmp[$menu->getId()]['url'] = $menu->getUrlForTwig();
                    $tmp[$menu->getId()]['active'] = explode(',', str_replace(PHP_EOL, '', $menu->getActive()));
                    $tmp[$menu->getId()]['child'] = array();
                }
            }

            foreach ($menusObjects as $menu) {
                $tmpChild = array();
                if (isset($menusIds[$menu->getId()])) {
                    if (isset($tmp[$menu->getParent()->getId()])) {
                        $tmpChild['name'] = $menu->getName();
                        $tmpChild['icon'] = $menu->getIcon();
                        $tmpChild['code'] = $menu->getCode();
                        $tmpChild['url'] = $menu->getUrlForTwig();
                        $tmpChild['active'] = explode(',', str_replace(PHP_EOL, '', $menu->getActive()));
                        $tmp[$menu->getParent()->getId()]['child'][] = $tmpChild;
                        $tmp[$menu->getParent()->getId()]['active'] = array_unique(array_merge(
                            $tmp[$menu->getParent()->getId()]['active'],
                            explode(',', str_replace(PHP_EOL, '', $menu->getActive()))
                        ));
                    }
                }
            }

            foreach ($tmp as $item) {
                if (isset($result[$item['category_id']])) {
                    $result[$item['category_id']]['child'][] = $item;
                } else {
                    $result[] = $item;
                }
            }

            $redisClient->hset('beast_menus', $menusKey, serialize($result));
            $redisClient->hset('beast_menus_controllers', $menusKey, serialize($controllers));
        }

        return $result;
    }
}

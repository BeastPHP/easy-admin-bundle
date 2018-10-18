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

namespace Beast\EasyAdminBundle\Form\Role;

use Beast\EasyAdminBundle\Entity\Authorization\Role;
use Beast\EasyAdminBundle\Entity\Menus\Menus;
use Beast\EasyAdminBundle\Repository\Menus\MenusRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RoleType
 *
 * @package Beast\EasyAdminBundle\Form\Role
 */
class RoleType extends AbstractType
{
    /**
     * @var MenusRepository
     */
    protected $menusRepository;

    /**
     * RoleType constructor.
     *
     * @param MenusRepository $menusRepository
     */
    public function __construct(MenusRepository $menusRepository)
    {
        $this->menusRepository = $menusRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => '角色名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '角色名称不能为空')
                    ),
                    new Assert\Length(
                        array(
                            'max' => '32',
                            'maxMessage' => '角色名称长度超过32位',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'permission',
            EntityType::class,
            array(
                'label' => '权限列表',
                'required' => true,
                'class' => Menus::class,
                'expanded' => false,
                'multiple' => true,
                'choice_label' => 'name',
                'attr' => array(
                    'size' => 10,
                ),
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('p');
                    return $query->orderBy('p.sort', 'ASC');
                },
                'group_by' => function (Menus $object, $key, $value) {
                    if ($object->isParent()) {
                        return $object->getName();
                    } else {
                        return $object->getParent()->getName();
                    }
                },
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Role::class,
                'csrf_protection' => true,
            )
        );
    }
}

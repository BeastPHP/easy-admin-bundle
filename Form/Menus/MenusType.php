<?php

namespace Beast\EasyAdminBundle\Form\Menus;

use Beast\EasyAdminBundle\Entity\Menus\Menus;
use Beast\EasyAdminBundle\Entity\Menus\MenusCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MenusType
 *
 * @package Beast\EasyAdminBundle\Form\Menus
 */
class MenusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);

        $builder->add(
            'parent',
            EntityType::class,
            array(
                'label' => '父级菜单',
                'required' => false,
                'class' => Menus::class,
                'expanded' => false,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('p');
                    return $query->where(
                        $query->expr()->isNull('p.parent')
                    )->orderBy('p.sort', 'ASC');
                },
            )
        );

        $builder->add(
            'menus_category',
            EntityType::class,
            array(
                'label' => '菜单组',
                'required' => false,
                'expanded' => false,
                'class' => MenusCategory::class,
                'choice_label' => 'name',
            )
        );

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => '菜单名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '菜单名称不能为空')
                    ),
                    new Assert\Length(
                        array(
                            'max' => '32',
                            'maxMessage' => '菜单名称长度超过32位',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'icon',
            TextType::class,
            array(
                'label' => '菜单图标',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(
                        array(
                            'max' => '64',
                            'maxMessage' => '菜单图标长度超过64位',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'url',
            TextareaType::class,
            array(
                'label' => '菜单控制器路径',
                'required' => true,
                'attr' => array('rows' => '9'),
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '菜单高亮地址不能为空')
                    ),
                    new Assert\Length(
                        array(
                            'max' => '128',
                            'maxMessage' => '菜单链接长度超过128位',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'active',
            TextareaType::class,
            array(
                'label' => '菜单高亮控制器路径',
                'required' => true,
                'attr' => array('rows' => '9'),
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '菜单高亮控制器路径不能为空')
                    ),
                ),
            )
        );

        $builder->add(
            'sort',
            TextType::class,
            array(
                'label' => '排序',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(
                        array(
                            'max' => '64',
                            'maxMessage' => '排序长度超过64位',
                        )
                    ),
                    new Assert\Regex(
                        array('message' => '请输入正整数', 'pattern' => '/^\d+$/')
                    ),
                ),
            )
        );

        $builder->add(
            'description',
            TextareaType::class,
            array(
                'label' => '菜单描述',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(
                        array(
                            'max' => '64',
                            'maxMessage' => '菜单描述长度超过64位',
                        )
                    ),
                ),
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
                'data_class' => Menus::class,
                'csrf_protection' => true,
            )
        );
    }
}

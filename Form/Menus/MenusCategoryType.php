<?php

namespace Beast\EasyAdminBundle\Form\Menus;

use Beast\EasyAdminBundle\Entity\Menus\MenusCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MenusCategoryType extends AbstractType
{
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
                'label'       => '菜单组名称',
                'required'    => true,
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '菜单组名称不能为空')
                    ),
                    new Assert\Length(
                        array(
                            'max'        => '32',
                            'maxMessage' => '菜单组名称长度超过32位',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'sort',
            TextType::class,
            array(
                'label'       => '排序',
                'required'    => false,
                'constraints' => array(
                    new Assert\Length(
                        array(
                            'max'        => '64',
                            'maxMessage' => '排序长度超过64位',
                        )
                    ),
                    new Assert\Regex(
                        array('message' => '请输入正整数', 'pattern' => '/^\d+$/')
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
        $resolver->setDefaults(array(
            'data_class'      => MenusCategory::class,
            'csrf_protection' => true,
        ));
    }
}

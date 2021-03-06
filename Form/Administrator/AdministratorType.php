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

namespace Beast\EasyAdminBundle\Form\Administrator;

use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Beast\EasyAdminBundle\Entity\Authorization\Role;
use Beast\EasyAdminBundle\Helper\Util;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AdministratorType
 *
 * @package Beast\EasyAdminBundle\Form\Administrator
 */
class AdministratorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $builder->add('id', HiddenType::class);

        $isActiveChoices = Util::getIsActiveChoices();
        $builder->add(
            'is_active',
            ChoiceType::class,
            array(
                'label' => '是否激活',
                'choices' => $isActiveChoices,
                'expanded' => true,
                'invalid_message' => '值是无效的',
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => array_values($isActiveChoices),
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'role',
            EntityType::class,
            array(
                'label' => '角色',
                'required' => true,
                'class' => Role::class,
                'expanded' => false,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('p');
                    return $query->orderBy('p.id', 'ASC');
                },
            )
        );

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => '姓名',
                'required' => false,
                'constraints' => array(),
            )
        );

        $builder->add(
            'email',
            TextType::class,
            array(
                'label' => '邮箱',
                'required' => false,
                'constraints' => array(
                    new Assert\Email(
                        array(
                            'message' => '请输入正确的邮箱地址',
                        )
                    ),
                ),
            )
        );

        $builder->add(
            'username',
            TextType::class,
            array(
                'label' => '账号',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(
                        array('message' => '账号不能为空')
                    ),
                    new Assert\Length(
                        array(
                            'max' => '32',
                            'maxMessage' => '账号长度超过32位',
                        )
                    ),
                ),
            )
        );

        if ($data->getId()) {
            $builder->add(
                'password',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'required' => false,
                    'invalid_message' => '两次密码不匹配',
                    'first_options' => array(
                        'label' => '密码',
                    ),
                    'second_options' => array(
                        'label' => '密码确认',
                    )
                )
            );
        } else {
            $builder->add(
                'password',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'invalid_message' => '两次密码不匹配',
                    'first_options' => array(
                        'label' => '密码',
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(
                                array('message' => '密码不能为空')
                            )
                        ),
                    ),
                    'second_options' => array(
                        'label' => '密码确认',
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(
                                array('message' => '密码确认不能为空')
                            )
                        ),
                    )
                )
            );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Administrator::class,
                'csrf_protection' => true,
            )
        );
    }
}

<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class RegistrationUserOperatorType
 * @package App\Form
 */
class RegistrationUserOperatorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Password',PasswordType::class,array('required'=>true))
            ->add('username', HiddenType::class)
            ->add('email')
            ->add(
                'enabled',
                ChoiceType::class,
                array(
                    'choices' => array(true => 'Sí', false => 'No'),
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
   

    /**
     * @return string
     */
    public function getName()
    {
        return 'useroperator_registration';
    }
}

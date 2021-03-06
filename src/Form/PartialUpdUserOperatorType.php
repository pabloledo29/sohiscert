<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Form;

use App\Entity\User;
use App\Entity\UserOperator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


/**
 * Class PartialUpdUserOperatorType
 * @package App\Form
 */
class PartialUpdUserOperatorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('current_password',PasswordType::class, ['required'=>true])
                ->add('password',RepeatedType::class,['type'=>PasswordType::class,
                        'required'=>true,
                        'empty_data' => '',
                        'invalid_message' => 'Las contraseñas deben coincidir',
                        'options' => ['attr' => [ 'class' => 'form-control','placeholder'=> '*******']],
                        'first_options'  => ['label' =>'Password:'],
                        'second_options' => ['label' => 'Confirmar password:']])->getForm() ;
        
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
        return 'private_useroperator_edit';
    }
}

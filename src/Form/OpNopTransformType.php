<?php

namespace App\Form;

use App\Entity\OpNopTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OpNopTransformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('opNop', TextType::class, ['label'=>'Número de expediente','attr' => ['placeholder' => 'LL-NNN/NNN-LL','class'=> 'form-control'],'required' =>true])
            ->add('opNopTransform', TextType::class, ['label'=>'Número de expediente trasnformado','attr'=>['placeholder' => 'LL-NNN-L','class'=> 'form-control'],'required' =>true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OpNopTransform::class,
        ]);
    }
}

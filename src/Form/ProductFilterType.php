<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priceRange', ChoiceType::class,
            [
                'choices'=> [
                    'Frouchette de prix'=> '[]',
                    '1€ - 29€' => '[1,30]',
                    '30€ - 34€' => '[30,35]',
                    '35€ - 50€' => '[35,50]',
                ],
                'label' => 'Filtre',
                'required'=> false,
                'data'=> '[]'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'method'=> 'GET',
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\SweatShirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SweatShirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('price', MoneyType::class, ['label' => 'Prix'])
            ->add('top', CheckboxType::class, ['label' => 'Top', 'required' => false])
            ->add('size', TextType::class, ['label' => 'Taille'])
            ->add('stock_xs', NumberType::class, ['label' => 'Stock XS'])
            ->add('stock_s', NumberType::class, ['label' => 'Stock S'])
            ->add('stock_m', NumberType::class, ['label' => 'Stock M'])
            ->add('stock_l', NumberType::class, ['label' => 'Stock L'])
            ->add('stock_xl', NumberType::class, ['label' => 'Stock XL'])
            ->add('image', FileType::class, [
              'label' => 'Image du sweat-shirt',
              'mapped' => false,
              'required' => true,
              'constraints' => [
                  new File([
                      'maxSize' => '1024k',
                      'mimeTypes' => [
                          'image/jpeg',
                          'image/png',
                      ],
                      'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                  ])
              ],
          ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SweatShirt::class,
        ]);
    }
}
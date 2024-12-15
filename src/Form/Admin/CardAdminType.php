<?php

namespace App\Form\Admin;

use App\Entity\Card;
use App\Entity\Product;
use App\Form\ProductAutocompleteField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CardAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', NumberType::class, ['help' => 'articulate'])
            ->add('product', ProductAutocompleteField::class)
            ->add('title', TextType::class)
            ->add('brand', TextType::class)
            ->add('price', NumberType::class)
            ->add('priceSale', NumberType::class)
            ->add('quantity', NumberType::class)
            ->add('weight', NumberType::class)
            ->add('countryCode', CountryType::class, ['required' => false])
            ->add('period', TextType::class)
            ->add('description', TextareaType::class)
            ->add('available', CheckboxType::class)
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG file', //TODO: use external validation message
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}

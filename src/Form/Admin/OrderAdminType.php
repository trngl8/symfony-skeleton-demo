<?php

namespace App\Form\Admin;

use App\Entity\Offer;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offer')
            ->add('deliveryEmail', EmailType::class, ['required'=>true])
            ->add('deliveryPhone', TextType::class, ['required'=>true])
            ->add('currency', CurrencyType::class, ['preferred_choices'=>['UAH', 'EUR', 'USD']])
            ->add('amount', NumberType::class)
            ->add('description', TextType::class, ['required'=>true])
            ->add('save', SubmitType::class, [
                'label' => 'Submit order'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}

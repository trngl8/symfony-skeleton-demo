<?php

namespace App\Form\Type;

use App\Model\Subscribe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'form.label.name'
                ])
            ->add('email', EmailType::class, [
                'label' => 'form.label.email'
            ])
            ->add('locale', LocaleType::class, [
                'label' => 'form.label.locale',
                'preferred_choices' => [
                    'uk' => 'uk',
                    'en' => 'en',
                    'ru' => 'ru'
                ]
            ])
            ->add('agree', CheckboxType::class, [
                'label' => 'form.label.agree'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.label.submit'
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscribe::class,
        ]);
    }

}

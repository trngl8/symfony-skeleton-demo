<?php

namespace App\Form\Admin;

use App\Entity\TimeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Deployer\add;

class TimeDataAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Primary' => 'primary',
                    'Secondary' => 'secondary',
                    'Success' => 'success',
                    'Warning' => 'warning',
                    'Danger' => 'danger',
                ],
            ])
            ->add('duration', NumberType::class)
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeData::class,
        ]);
    }
}

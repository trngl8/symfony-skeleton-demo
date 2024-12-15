<?php

namespace App\Form;

use App\Entity\Invite;
use App\Model\InviteAccept;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteAcceptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.label.email',
                'disabled' => true
            ])
            ->add('name', TextType::class, [
                'label' => 'form.label.name',
                'disabled' => true
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
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InviteAccept::class,
        ]);
    }

}

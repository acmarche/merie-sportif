<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Security\RoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $roles = RoleEnum::all();
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [

                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'required' => true,
                    'label' => "Nom d'utilisateur",
                ]
            )
            ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}

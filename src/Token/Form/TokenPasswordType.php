<?php

namespace AcMarche\MeriteSportif\Token\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TokenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('password', TextType::class, [
                'label' => 'Mot de passe',
            ]);
    }
}

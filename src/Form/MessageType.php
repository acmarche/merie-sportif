<?php

namespace AcMarche\MeriteSportif\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('sujet', TextType::class)
            ->add(
                'texte',
                TextareaType::class,
                [
                    'attr' => ['rows' => 5],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}

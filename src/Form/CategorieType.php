<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'ordre',
                IntegerType::class,
                [
                    'help' => "Ordre d'affichage pour le vote",
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [

                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Categorie::class,
            ]
        );
    }
}

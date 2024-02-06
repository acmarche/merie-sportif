<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom du candidat ou de l\'équipe, club',
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Prénom',
                ]
            )
            ->add(
                'description',
                CKEditorType::class,
                [
                    'config_name' => 'sepulture_config',
                    'attr' => [
                        'rows' => 8,
                    ],
                    'help' => 'Son parcours / Historique du candidat',
                ]
            )
            ->add(
                'palmares',
                CKEditorType::class,
                [
                    'config_name' => 'sepulture_config',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'sport',
                TextType::class,
                [
                    'label' => 'Sport',
                    'required' => true,
                    'help' => '(Trail - Jogging, Athlétisme, Judo, Basket-ball, Tennis de table, Football,...',
                ]
            )
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                    'multiple' => false,
                    'expanded' => true,
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'required' => false,
                    'label' => 'Image',
                    'constraints' => [
                        new File([
                            'maxSize' => '3000k',
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                        ]),
                    ],
                ]
            )
            ->add(
                'validate',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Validé',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Candidat::class,
            ]
        );
    }
}

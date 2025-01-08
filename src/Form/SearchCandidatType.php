<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\Categorie;
use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCandidatType extends AbstractType
{
    public function __construct(private readonly CandidatRepository $candidatRepository)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $sports = $this->candidatRepository->getAllSports();

        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                    'required' => false
                ]
            )
            ->add(
                'sport',
                ChoiceType::class,
                [
                    'choices' => $sports,
                    'required' => false
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [

            ]
        );
    }
}

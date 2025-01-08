<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Validator\Vote as VoteValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class VotesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add(
            'candidatures',
            CollectionType::class,
            [
                'entry_type' => VoteType::class,
                'label'=>false,
                'constraints' => [
                    new VoteValidator(),
                ],
            ]
        );

    }
}

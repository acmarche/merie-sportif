<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use AcMarche\MeriteSportif\Validator\Vote as VoteValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class VotesType extends AbstractType
{
    public function __construct(private CandidatRepository $candidatRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
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

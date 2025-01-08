<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatHiddenType extends AbstractType
{
    public function __construct(private readonly CandidatToNumberTransformer $candidatToNumberTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->addModelTransformer($this->candidatToNumberTransformer);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'invalid_message' => 'The selected candidat does not exist',
            ]
        );
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }


}

<?php

namespace AcMarche\MeriteSportif\Form;

use AcMarche\MeriteSportif\Entity\Setting;
use AcMarche\MeriteSportif\Setting\SettingEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('year', IntegerType::class, [
                'label' => 'Année du Mérite sportif',
            ])
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => SettingEnum::modes(),
            ])
            ->add('emailFrom', EmailType::class, [
                'label' => 'Email expéditeur',
                'help' => "Les mails seront envoyés depuis cette adresse",
            ])
            ->add('emails', CollectionType::class, [
                'label' => 'Emails destinataires',
                'help' => 'Destinataires des notifications',
                'entry_type' => EmailType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Setting::class,
            ],
        );
    }
}

<?php

namespace App\Form;

use App\Entity\Dossier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de demande',
                'choices' => [
                    'Achat' => 'achat',
                    'Location' => 'location',
                ],
                'placeholder' => 'Choisir...',
            ])
            ->add('options', ChoiceType::class, [
                'label' => 'Options (location uniquement)',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'choices' => [
                    'Assurance tous risques' => 'Assurance tous risques',
                    'Assistance dépannage' => 'Assistance dépannage',
                    'Entretien et SAV' => 'Entretien et SAV',
                    'Contrôle technique' => 'Contrôle technique',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);
    }
}
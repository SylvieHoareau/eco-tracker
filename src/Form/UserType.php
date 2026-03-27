<?php

namespace App\Form;

use App\Document\User;
use App\Form\EcoActionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $communes = [
            'Les Avirons' => 'Les Avirons',
            'Bras-Panon' => 'Bras-Panon',
            'Cilaos' => 'Cilaos',
            'Entre-Deux' => 'Entre-Deux',
            'L\'Étang-Salé' => 'L\'Étang-Salé',
            'La Plaine-des-Palmistes' => 'La Plaine-des-Palmistes',
            'Le Port' => 'Le Port',
            'La Possession' => 'La Possession',
            'Saint-André' => 'Saint-André',
            'Saint-Benoît' => 'Saint-Benoît',
            'Saint-Denis' => 'Saint-Denis',
            'Saint-Joseph' => 'Saint-Joseph',
            'Saint-Leu' => 'Saint-Leu',
            'Saint-Louis' => 'Saint-Louis',
            'Saint-Paul' => 'Saint-Paul',
            'Saint-Philippe' => 'Saint-Philippe',
            'Saint-Pierre' => 'Saint-Pierre',
            'Sainte-Marie' => 'Sainte-Marie',
            'Sainte-Rose' => 'Sainte-Rose',
            'Sainte-Suzanne' => 'Sainte-Suzanne',
            'Salazie' => 'Salazie',
            'Le Tampon' => 'Le Tampon',
            'Les Trois-Bassins' => 'Les Trois-Bassins',
        ];
        asort($communes); // Tri alphabétique sur les valeurs

        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => true, // Le champ correspond à la propriété $password du Document
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('commune', ChoiceType::class, [
                'choices' => $communes,
                'placeholder' => 'Choisissez votre commune',
                'label' => 'Votre commune de résidence',
                'attr' => ['class' => 'form-select form-control-lg'] // Classe Bootstrap
            ]);
            // ->add('actions', CollectionType::class, [
            //     'entry_type' => EcoActionType::class,
            //     'allow_add' => true,
            //     'by_reference' => false, // OBLIGATOIRE pour que Symfony appelle addAction()
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
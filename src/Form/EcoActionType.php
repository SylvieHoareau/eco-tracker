<?php

namespace App\Form;

use App\Document\EcoAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;  

class EcoActionType extends AbstractType
{       
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Nom de l\'action'])
            ->add('carbonSaved', IntegerType::class, ['label' => 'CO2 économisé (g)'])
            // On ne met pas le champ "user" ici, on le gérera dans le contrôleur
        ;
    }
}
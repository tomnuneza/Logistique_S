<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' =>"Nom"])
            ->add('reference' , TextType::class, ['label' =>"Reference"])
            ->add('typeConditionnement', TextType::class, ['label' =>"Conditionnement"])
            ->add('quantite', IntegerType::class, ['label' =>"Quantity"])
            ->add('emplacement', TextType::class, ['label' =>"L'Emplacement"])
            ->add('prix', NumberType::class, ['scale' => 2 ,'label' =>"Prix"])
            ->add('quota' , IntegerType::class, ['label' =>"Quota"])
            ->add('stock' , IntegerType::class, ['label' =>"Stock"])
            ->add('estActif'  , CheckboxType::class, ['label' =>"Actif"])
            // ->add('dateCreation', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('dateMaj', null, [
            //     'widget' => 'single_text'
            // ])
            ->add('submit', SubmitType::class, ['label' => "Valider"])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fournisseur', EntityType::class, [
            'class' => Fournisseur::class,
            'choices' => $options['fournisseurs'],
            'choice_label' => 'nom',
            'label' => "Fournisseur"
        ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choices' => $options['categories'],
                'choice_label' => 'nom',
                'label' => "Catégorie"
            ])
            ->add('nom', TextType::class, ['label' => "Nom"])
            ->add('reference', TextType::class, ['label' => "Reference"])
            ->add('typeConditionnement', TextType::class, ['label' => "Conditionnement"])
            ->add('quantite', IntegerType::class, ['label' => "Quantity"])
            ->add('emplacement', TextType::class, ['label' => "L'Emplacement"])
            ->add('prix', MoneyType::class, ['divisor' => 100, 'currency' => false , 'label' => "Prix"])
            ->add('quota', IntegerType::class, ['label' => "Quota"])
            ->add('stock', IntegerType::class, ['label' => "Stock"])
            ->add('estActif', CheckboxType::class, ['label' => "Actif", 'required' => false])
            ->add('imageFile', VichImageType::class, ['label' => "Image", 'required' => false])
            ->add('submit', SubmitType::class, ['label' => "Valider"])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'fournisseurs' => null,
            'categories' => null,
        ]);
    }
}

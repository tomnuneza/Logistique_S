<?php

namespace App\Form;

use App\Entity\PointDeVente;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, ['label' => "Email"])
            ->add('roles', ChoiceType::class, [
                // 'class' => User::class,
                // 'choices' => $options['roles']
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_ACHETEUR' => 'ROLE_ACHETEUR',
                    'ROLE_LOGISTICIEN' => 'ROLE_LOGISTICIEN',
                    'ROLE_GESTIONNAIRE' => 'ROLE_GESTIONNAIRE'
                ],
                'expanded' => false,
                'multiple' => true,
                'label' => 'Rôle',

            ])
            ->add('password', PasswordType::class, ['mapped' => false, 'label' => "Mot de passe"])
            ->add('nom', TextType::class, ['label' => "Nom"])
            ->add('prenom', TextType::class, ['label' => "Prénom"])
            ->add('telephone', NumberType::class, ['label' => "Téléphone"])
            ->add('estActif', CheckboxType::class, ['label' => "Actif"])
            ->add('pointDeVente', EntityType::class, [
                'class' => PointDeVente::class,
                'choices' => $options['pointDeVentes'],
                'choice_label' => 'nom',
                'label' => 'Point de vente',
            ])
            ->add('submit', SubmitType::class, ['label' => "Valider"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'pointDeVentes' => null
        ]);
    }
}

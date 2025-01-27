<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class,[
                'label'=>'Votre Prénom',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre Prénom..'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label'=>'Votre Nom',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre Nom..'
                ]
            ])
            ->add('address',TextType::class,[
                'label'=>'Votre Adresse',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre Adresse..'
                ]
            ])
            ->add('postal',TextType::class,[
                'label'=>'Votre Code postale',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre Code postale..'
                ]
            ])
            ->add('city',TextType::class,[
                'label'=>'Votre Ville',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre Ville..'
                ]
            ])
            ->add('country',CountryType::class,[
                'label'=>'Votre pays',
                'attr'=>[
                    'placeholder'=>'Indiquez Votre pays.'
                ]
            ])
            ->add('phone',TextType::class,[
                'label'=>'Votre téléphone',
                'attr'=>[
                    'placehoder'=>'Indiquez Votre numéro de téléphone..'
                ]
            ])
            ->add('submit',SubmitType::class,['label'=>"Sauvgarder ",
            'attr'=>[
                'class' =>'btn btn-success'
            ]
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}

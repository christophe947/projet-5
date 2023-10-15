<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
//use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\Form\Extension\Core\Type\TextAreaType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
//use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
//use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class , [
                'label' => 'E-mail', 
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez indiqer un e-mail valide',
                    ]),
                    new NotBlank([
                        'message' => 'Indiquez votre e-mail',
                    ])
                    
                ]
            ])
            /*->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Mot de passe *'],
                'invalid_message' => 'Les mots de passes doivent etre identiques',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Indiquez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ]
            ])*/
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo' ,
                'constraints' => [
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]              
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer votre Nom de famille',
                    ]),
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer votre Prénom',
                    ]),
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Anniversaire',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer votre date de naissance',
                    ])
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]
            ])
            ->add('street', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 50,
                    ])
                ]
            ])
            ->add('info', TextareaType::class, [
                'label' => 'Information',
                'constraints' => [
                    new Length([
                        'maxMessage' => 'maximum : {{ limit }} caracteres',
                        'max' => 500,
                    ])
                ]              
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Visibilitée',
                'choices'  => [
                    'Privée' => 1,
                    'Publique' => 2,
                    'Amis seulement' => 3,
                ],
            ])
            //->add('status', HiddenType::class)
            //->add('created_at', HiddenType::class)
            //->add('updated_at', HiddenType::class)
            
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
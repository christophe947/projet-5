<?php

namespace App\Form;

use App\Entity\User;
//use DateTimeInterface;
//use Doctrine\DBAL\Types\DateType as TypesDateType;
//use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
//use Symfony\Component\Validator\Constraints\Email;
//use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
//use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class , [
                'label' => 'Email', 
                //'attr' => ['unique' => 'true'],
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez indiqer un email valide',
                    ]),
                    new NotBlank([
                        'message' => 'Indiquez votre Email',
                    ])
                    
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Pass',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Indiquez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer votre Nom',
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prenom',                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer votre Prenom',
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
            ->add('status', HiddenType::class)
            ->add('created_at', HiddenType::class)
            ->add('updated_at', HiddenType::class)
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter les conditions',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ])
                ]
            ])
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
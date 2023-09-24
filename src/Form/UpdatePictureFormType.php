<?php

namespace App\Form;

use App\Entity\Picture;
use App\Entity\Album;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePictureFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('album', EntityType::class, [
                'label' => 'Album ( facultatif )',  
                'choices' => $options['album'],
                'required' => false,
                'class' => Album::class,            
            ])
            ->add('legend', TextType::class, [
                'label' => 'Legende',
                //'required' => true,             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer une legende'
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 10,
                    ])
                ]
            ])
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif',              
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez indiquer une courte description de l'image"
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',              
            ])
            ->add('Upload', SubmitType::class, [
                'label' => 'Metre a jour',              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'album' => false,
            //'legend' => '',
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
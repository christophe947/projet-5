<?php

namespace App\Form;

use App\Entity\Music;
use App\Entity\Album;
//use App\Entity\User;

//use Doctrine\Persistence\ManagerRegistry;


//use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MusicFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', HiddenType::class)
            ->add('music', FileType::class, [
                'label' => 'Choisir fichier',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez selectionner un fichier a telecharger'
                    ]),
                    new File([
                        'maxSize' => '100000000', //tester avec 
                        'maxSizeMessage' => 'La taille du fichier est invalide maximum :  {{ limit }} ko.',
                        'mimeTypes' => [
                            'audio/mpeg',
                            'audio/mp4',
                            'audio/ogg',
                            'audio/vnd.wav',
                            'audio/mid'
                        ],
                        'mimeTypesMessage' => 'Le format du fichier : {{ type }} est invalid. Formats attendu :  {{ types }}.',
                    ])
                ],
            ])
            /*->add('description', TextType::class, [
                'label' => 'Description',             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer une legende'
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
            ])*/
            ->add('album', EntityType::class, [
                'label' => 'Album ( facultatif )',  
                'choices' => $options['album'],
                'required' => false,
                'class' => Album::class,            
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer un titre'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',              
            ])
            ->add('status', HiddenType::class)
            ->add('created_at', HiddenType::class)
            ->add('updated_at', HiddenType::class)
            ->add('Upload', SubmitType::class, [
                'label' => 'Telecharger',              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Music::class,
            'album' => false,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
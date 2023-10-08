<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('url', TextType::class, [
                'label' => 'Integration',             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer un contenu'
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer un titre'
                    ])
                ]
            ])
            ->add('legend', TextType::class, [
                'label' => 'Legende',             
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiqer une legende'
                    ])
                ]
            ])
            ->add('status', HiddenType::class)
            ->add('album', EntityType::class, [
                'label' => 'Album ( facultatif )', 
                'choices' => $options['album'],  
                'required' => false,
                'class' => Album::class
                
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',              
            ])
            //->add('album_name')
            ->add('created_at', HiddenType::class)
            ->add('updated_at', HiddenType::class)
            ->add('Upload', SubmitType::class, [
                'label' => 'Telecharger'      
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'album' => false,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
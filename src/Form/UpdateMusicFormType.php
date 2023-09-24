<?php

namespace App\Form;

use App\Entity\Music;
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

class UpdateMusicFormType extends AbstractType
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
            ->add('Upload', SubmitType::class, [
                'label' => 'Metre a jour',              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Music::class,
            'album' => false,
            //'legend' => '',
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
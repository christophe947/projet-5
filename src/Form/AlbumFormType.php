<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class AlbumFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('album_name', TextType::class, [
                'label' => "Nouvel album :",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez indiquer un nom d'album"
                    ])
                ]           
            ])
            ->add('created_at', HiddenType::class)
            ->add('updated_at', HiddenType::class)
            ->add('Create', SubmitType::class, [
                'label' => 'Creer album',              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
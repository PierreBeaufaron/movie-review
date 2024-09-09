<?php

namespace App\Form;

use App\Entity\Director;
use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',  // Utilisation d'un input avec un calendrier
                'html5' => true,            // Active l'input de type date natif en HTML5
                'attr' => [
                    'class' => 'js-datepicker',  // Classe CSS pour personnalisation si nécessaire
                ],
                //'format' => 'dd-MM-yyyy',   // Format de la date
                'label' => 'Date de sortie',
                'required' => true,
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Durée en minutes',
                'required' => true,
            ])
            ->add('synopsis', TextareaType::class)
            ->add('imgSrcUrl', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
            ])
            ->add('director', TextType::class, [
                'label' => 'Réalisateur',
                'required' => true,
                'mapped' => false,
            ])
            ->add("Ajouter", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}

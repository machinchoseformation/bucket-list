<?php

namespace App\Form;

use App\Entity\Wish;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                "label" => "Votre idée"
            ])
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'expanded' => true,
                'label' => "Catégorie",
            ])
            ->add('description', TextareaType::class, [
                "required" => false,
                "label" => "Et plus en détails ?",
            ])
            ->add('imageFile', FileType::class, [
                "required" => false,
                "label" => "Une image pour représenter votre idée",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}

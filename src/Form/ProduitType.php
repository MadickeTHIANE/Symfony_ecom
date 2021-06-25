<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Produit;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix'
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'choice_label' => 'name',
                'class' => Tag::class,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'choice_label' => 'name',
                'class' => Category::class,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'style' => 'margin-top : 5px',
                    'class' => 'btn btn-success',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

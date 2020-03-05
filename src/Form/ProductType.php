<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Verifier si on ajoute ou on modifie un produit (pour l'image)
        $produit = $options['data'] ?? null;
        $modification = $produit && $produit->getId();


        $imgConstraints = [new Image()];
        if(!$modification){
            $imgConstraints[] = new NotNull(['message' => 'Veuillez envoyer une image']);
        }

        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer un titre']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'le titre ne peut contenir au moins 3 caractères',
                        'max' => 100,
                        'maxMessage' => 'le titre ne peut contenir plus de 100 caractères',
                    ])
                ]
            ])
            ->add('description', TextareaType::class,[
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer une description']),
                ]
            ])
            ->add('stock', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer un stock']),
                    new PositiveOrZero(['message'=> 'Le stock ne peut etre negatif'])
                ]
            ])
            ->add('price', MoneyType::class,[
                'currency' => 'EUR',
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer un prix']),
                    new Positive(['message'=> 'Le prix doit etre positif'])
                ]
            ])
            ->add('imageUpload', FileType::class,[
                'mapped' => false,
                'required' => false,
                'constraints' => $imgConstraints
            ])
            ->add('size', ChoiceType::class,[
                'choices' => [
                    6 => 6,
                    7=> 7,
                    8=> 8,
                    9=> 9,
                    10=> 10,
                    11=> 11,
                    12=> 12 ]
            ])
            ->add('color', TextType::class,[
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer une couleur']),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'le titre ne peut contenir plus de 20 caractères',
                    ])
                ]
            ])
            ->add('brand', TextType::class,[
                'constraints' => [
                    new NotBlank(['message'=>'Veuillez indiquer une marque']),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'la marque ne peut contenir plus de 50 caractères',
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'multiple' => true,
                'expanded' => true,
                'class'=> Category::class,
                'choice_label' => 'title',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez selectionnez au moins une catégorie',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

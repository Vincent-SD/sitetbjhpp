<?php

namespace App\Form;

use App\Entity\Marais;
use App\Entity\Marche;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'multiple' => true,
                'expanded' => false
            ])

            ->add('marais', EntityType::class, [
                'class' => Marais::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
        ]);
    }
}

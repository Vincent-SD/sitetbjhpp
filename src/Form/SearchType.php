<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType as SearchInputType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', SearchInputType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une bierasse',
                    'class' => 'form-control'
                ],
            ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

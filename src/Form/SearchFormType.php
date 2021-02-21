<?php

namespace App\Form;

use App\Entity\Marque;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prixMin', NumberType::class,['required' => false])
            ->add('prixMax', NumberType::class,['required' => false])
            ->add('marque', EntityType::class,[
               'class' => Marque::class,
                'required' => false
            ]);
    }
}

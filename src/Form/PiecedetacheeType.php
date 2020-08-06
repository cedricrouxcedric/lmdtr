<?php

namespace App\Form;

use App\Entity\Piecedetachee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PiecedetacheeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Piece'
            ])
            ->add('marque', null, [
                'choice_label' => 'name',
                'label' => 'marque de  moto'
            ])
            ->add('model',null, [
                'label' => 'modele de moto'
            ])
            ->add('description')
            ->add('prix')
            ->add('images', FileType::class, [
                'label' => "Photos",
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Piecedetachee::class,
        ]);
    }
}

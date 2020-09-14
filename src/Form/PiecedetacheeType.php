<?php

namespace App\Form;

use App\Entity\Piecedetachee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('cylindreemoto',IntegerType::class,['label' => 'cylindrée en cm³','attr'=>array('min'=> 50,'max'=>6200,'class'=>'cylindreInput')])
            ->add('anneemoto',IntegerType::class,['label' => 'Année','attr'=>array('min'=>1868,'max'=>date("Y")+1)])
            ->add('usure',IntegerType::class,['label'=>'Taux d\'usure en %','attr'=>array('min'=> 0,'max'=>100,'step'=>5)])
            ->add('description')
            ->add('prix')
            ->add('images', FileType::class, [
                'label' => "Photos",
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('showphonenumber',null,['label'=>"Afficher telephone dans l'annonce"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Piecedetachee::class,
        ]);
    }
}

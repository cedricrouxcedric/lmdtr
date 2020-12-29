<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Moto;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotNull;

class MotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marque',null,['choice_label' => 'name','attr'=>array('class'=>'col-9 m-3')])
            ->add('model',TextType::class,['attr'=>array('class'=>'col-9 m-3')])
            ->add('cylindree',IntegerType::class,['label' => 'cylindrée en cm³','attr'=>array('min'=> 50,'max'=>6200,'class'=>'cylindreInput col-9 m-3')])

            ->add('year',IntegerType::class,['label' => 'Année','attr'=>array('class'=>'col-9 m-3','min'=>1868,'max'=>date("Y")+1),])
            ->add('kilometrage',IntegerType::class,['attr'=>array('class'=>'col-9 m-3')])
            ->add('din',IntegerType::class,['label' => 'puissance','attr'=>array('class'=>'col-9 m-3')])
            ->add('fisc',IntegerType::class,['label' => 'puissance fiscal','attr'=>array('class'=>'col-9 m-3')])
            ->add('categorie',null,['choice_label' => 'name','attr'=>array('class'=>'col-6 m-3')])
            // Ajout du champ "IMAGES" au formulaire n'etant pas lié a la bdd (mapped false)
            ->add('images', FileType::class, [
                'label' => "Photos",
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr'=>array('class'=>'col-6 m-3')
            ])
            ->add('a2',CheckboxType::class,[
                'label' => 'Bridable',
                'required' => false])
            ->add('prix')
        ->add('description',TextareaType::class)
            ->add('showphonenumber',null,['label'=>"Afficher telephone dans l'annonce"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moto::class,
        ]);
    }
}

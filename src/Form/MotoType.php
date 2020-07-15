<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Moto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class MotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prix')
            ->add('year',null,['label' => 'Année'])
            ->add('kilometrage')
            ->add('din',null,['label' => 'puissance'])
            ->add('fisc',null,['label' => 'puissance fiscal' ])
            ->add('categorie',null,['choice_label' => 'name'])
            ->add('marque',null,['choice_label' => 'name'])
            // Ajout du champ "IMAGES" au formulaire n'etant pas lié a la bdd (mapped false)
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moto::class,
        ]);
    }
}

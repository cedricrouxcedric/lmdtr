<?php

namespace App\Form;

use App\Entity\Departments;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\Towns;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);
        $builder
            /* @var $departement Departments */
            ->add('departement', EntityType::class, [
                'class' => 'App\Entity\Departments',
                'placeholder' => 'Sélectionnez votre département',
                'mapped' => false,
                'required' => false
            ]);
        $builder->addEventListener(FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                /* @var $town Towns */
                $town = $data->getTown();
                $departement = $town ? $town->getDepartmentCode() : null;
                if ($town) {
                    /* @var $departement Departments */
                    $form = $event->getForm();
                    $form->get('departement')->setData($departement);
                    $form->add('town', EntityType::class, [
                        'class' => 'App\Entity\Towns',
                        'mapped' => true,
                        'required' => true,
                        'choices' => $departement->getTowns(),])->setData($town);
                }
            });
        $builder->get('departement')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $form->getParent()->add('town', EntityType::class, [
                    'class' => 'App\Entity\Towns',
                    'placeholder' => 'Choissisez votre ville',
                    'mapped' => true,
                    'required' => true,
                    'choices' => $form->getData()->getTowns(),]);
            }
        );
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'label'      => 'Role ',
                'expanded' => true,
                'multiple'=> true,
                'choices' => array(
                    'Sac de sable' => 'ROLE_USER',
                    'Motard' => 'ROLE_SUBSCRIBER',
                    'Pilote' => 'ROLE_ADMIN',
                )
                ])
            ->add('validateAccount')
            ->add('cgu')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

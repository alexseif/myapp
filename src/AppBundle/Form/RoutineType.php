<?php

namespace AppBundle\Form;

use AppBundle\Entity\Routine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoutineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('priority')
            ->add('sort')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Disabled' => 'disabled'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Routine::class,
        ]);
    }
}

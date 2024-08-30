<?php

namespace App\Form;

use App\Entity\Tasks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskSizingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task')
            ->add('est', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Est',
                    'class' => 'est',
                ],
            ])
            ->add('priority', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline'],
            ])
            ->add('urgency', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Normal' => 0,
                    'Urgent' => 1,
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline'],
            ])
            ->add('eta', DateTimeType::class, [
                'label' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy - MM - dd',
                'required' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data - provide' => 'datepicker',
                    'data - date - format' => 'yyyy - MM - dd',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}

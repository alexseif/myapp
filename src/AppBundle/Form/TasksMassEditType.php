<?php

namespace AppBundle\Form;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksMassEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline'],
            ])
            ->add('urgency', ChoiceType::class, [
                'choices' => [
                    'Not Urgent' => 0,
                    'Urgent' => 1,
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline'],
            ])
            ->add('taskList', EntityType::class, ['class' => TaskLists::class, 'choice_label' => 'name']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}

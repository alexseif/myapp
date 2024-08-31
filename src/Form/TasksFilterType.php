<?php

namespace App\Form;

use App\Entity\TaskLists;
use App\Repository\TaskListsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksFilterType extends AbstractType
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
            'multiple' => true,
            'required' => false,
            'label_attr' => ['class' => 'checkbox-inline'],
          ])
          ->add('urgency', ChoiceType::class, [
            'choices' => [
              'Normal' => 0,
              'Urgent' => 1,
            ],
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'label_attr' => ['class' => 'checkbox-inline'],
          ])
          ->add('completed', ChoiceType::class, [
            'choices' => [
              'Completed' => 1,
              'Not Completed' => 0,
            ],
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'label_attr' => ['class' => 'checkbox-inline'],
          ])
          ->add('taskList', EntityType::class, [
            'class' => TaskLists::class,
            'query_builder' => function (TaskListsRepository $er) {
                return $er->createQueryBuilder('tl')
                  ->select('tl, a, c')
                  ->leftJoin('tl.account', 'a')
                  ->leftJoin('a.client', 'c');
            },
            'group_by' => function ($taskList) {
                if ($taskList->getAccount()) {
                    if ($taskList->getAccount()->getClient()) {
                        return $taskList->getAccount()->getClient()->getName();
                    }

                    return $taskList->getAccount()->getName();
                }

                return 'N/A';
            },
            'choice_label' => 'name',
            'multiple' => true,
            'required' => false,
            'attr' => [
              'class' => 'chosen',
            ],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'csrf_protection' => false,
            //      'data_class' =>Tasks::class
        ]);
    }

}

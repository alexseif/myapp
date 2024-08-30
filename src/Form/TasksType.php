<?php

namespace App\Form;

use App\Entity\TaskLists;
use App\Entity\Tasks;
use App\Repository\TaskListsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task')
            ->add('taskList', EntityType::class, [
                'class' => TaskLists::class,
                'label' => false,
                'query_builder' => function (TaskListsRepository $er) {
                    return $er->createQueryBuilder('tl')
                      ->select('tl, a, c')
                      ->leftJoin('tl.account', 'a')
                      ->leftJoin('a.client', 'c')
                        ->where('tl.status <> \'archive\'');
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
                'attr' => [
                    'class' => 'chosen',
                ],
            ])
            ->add('est')
            ->add('duration')
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
            ->add('order', HiddenType::class)
            ->add('eta', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                ],
            ])
            ->add('completed')
            ->add('completedAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}

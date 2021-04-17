<?php

namespace AppBundle\Form;

use AppBundle\Entity\Tasks;
use AppBundle\Repository\TaskListsRepository;
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
            ->add('taskList', EntityType::class, array(
                'class' => 'AppBundle:TaskLists',
                'label' => false,
                'query_builder' => function (TaskListsRepository $er) {
                    return $er->createQueryBuilder('tl')
                        ->where('tl.status <> \'archive\'');
                },
                'group_by' => function ($taskList) {
                    if ($taskList->getAccount()) {
                        if ($taskList->getAccount()->getClient()) {
                            return $taskList->getAccount()->getClient()->getName();
                        }
                        return $taskList->getAccount()->getName();
                    }
                    return "N/A";
                },
                'choice_label' => 'name',
                'attr' => array(
                    'class' => 'chosen',
                )
            ))
            ->add('est')
            ->add('duration')
            ->add('priority', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ),
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('urgency', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Normal' => 0,
                    'Urgent' => 1
                ),
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('order', HiddenType::class)
            ->add('eta', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                )
            ))
            ->add('completed')
            ->add('completedAt', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}

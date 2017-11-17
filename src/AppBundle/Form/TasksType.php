<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TasksType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('task')
        ->add('est')
        ->add('priority', ChoiceType::class, array(
          'choices' => array(
            'Low' => -1,
            'Normal' => 0,
            'Important' => 1,
          ),
          'choices_as_values' => true,
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('urgency', ChoiceType::class, array(
          'choices' => array(
            'Normal' => 0,
            'Urgent' => 1
          ),
          'choices_as_values' => true,
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('taskList', EntityType::class, array(
          'class' => 'AppBundle:TaskLists',
          'query_builder' => function (\AppBundle\Repository\TaskListsRepository $er) {
            return $er->createQueryBuilder('tl')
                ->where('tl.status <> \'archive\'');
          },
          'group_by' => function($taskList) {
            if ($taskList->getAccount()) {
              if ($taskList->getAccount()->getClient()) {
                return $taskList->getAccount()->getClient()->getName();
              }
              return $taskList->getAccount()->getName();
            }
            return "N/A";
          },
          'choice_label' => 'name'
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
        ))
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => \AppBundle\Entity\Tasks::class
    ));
  }

}

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
            'Not Important' => 0,
            'Important' => 1,
          ),
          'choices_as_values' => true,
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('urgency', ChoiceType::class, array(
          'choices' => array(
            'Not Urgent' => 0,
            'Urgent' => 1
          ),
          'choices_as_values' => true,
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('taskList', EntityType::class, array(
          'class' => 'AppBundle:TaskLists',
          'group_by' => function($taskList) {
            if ($taskList->getAccount()) {
              return $taskList->getAccount()->getClient()->getName();
            } else{
              return "N/A";
            }
          },
          'choice_label' => 'name'
        ))
        ->add('order', HiddenType::class)
        ->add('eta', DateTimeType::class, array(
          'date_widget' => 'single_text',
          'time_widget' => 'single_text',
          'required' => false
        ))
        ->add('completed')
        ->add('completedAt', DateTimeType::class, array(
          'date_widget' => 'single_text',
          'time_widget' => 'single_text',
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

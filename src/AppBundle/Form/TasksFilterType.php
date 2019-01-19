<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TasksFilterType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('priority', ChoiceType::class, array(
          'choices' => array(
            'Low' => -1,
            'Normal' => 0,
            'Important' => 1,
          ),
          'expanded' => true,
          'multiple' => true,
          'required' => false,
          'label_attr' => array('class' => 'checkbox-inline')
        ))
        ->add('urgency', ChoiceType::class, array(
          'choices' => array(
            'Normal' => 0,
            'Urgent' => 1
          ),
          'expanded' => true,
          'multiple' => true,
          'required' => false,
          'label_attr' => array('class' => 'checkbox-inline')
        ))
        ->add('completed', ChoiceType::class, array(
          'choices' => array(
            'Completed' => 1,
            'Not Completed' => 0,
          ),
          'expanded' => true,
          'multiple' => true,
          'required' => false,
          'label_attr' => array('class' => 'checkbox-inline')
        ))
        ->add('taskList', EntityType::class, array(
          'class' => 'AppBundle:TaskLists',
          'group_by' => function($taskList) {
            if ($taskList->getAccount()) {
              if ($taskList->getAccount()->getClient()) {
                return $taskList->getAccount()->getClient()->getName();
              }
              return $taskList->getAccount()->getName();
            }
            return "N/A";
          },
          'choice_label' => 'name',
          'multiple' => true,
          'required' => false,
          'attr' => array(
            'class' => 'chosen',
          )
        ))
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'csrf_protection' => false,
//      'data_class' => \AppBundle\Entity\Tasks::class
    ));
  }

}

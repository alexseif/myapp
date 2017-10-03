<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Repository\TasksRepository;

class PlannerType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('due', DateType::class, array(
          // render as a single text box
          'widget' => 'single_text',
          'attr' => array(
            'min' => date('Y-m-d')
      )))
        ->add('tasks', EntityType::class, array(
          'required' => false,
          'class' => 'AppBundle:Tasks',
          'query_builder' => function (TasksRepository $er) {
            return $er->createQueryBuilder('t')
                ->where('t.completed <> true')
                ->orderBy('t.order', 'ASC');
          },
          'choice_label' => function ($task) {
            return $task->getTask() . " (" . $task->getTaskList() . ")";
          },
          'multiple' => true,
          'group_by' => function($val, $key, $index) {
            return $val->getTaskList();
          },
          'attr' => array('class' => 'chosen')
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Planner'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_planner';
  }

}

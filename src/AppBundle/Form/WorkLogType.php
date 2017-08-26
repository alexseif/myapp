<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WorkLogType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('task', EntityType::class, array(
          'class' => 'AppBundle:Tasks',
          'group_by' => function($tasks) {
            return $tasks->getTaskList()->getName();
          },
          'choice_attr' => function($tasks, $key, $index) {
            return ['data-est' => $tasks->getEst()];
          },
          'attr' => array(
            'class' => 'chosen'
          )
        ))
        ->add('name')
        ->add('duration')
        ->add('pricePerUnit')
        ->add('total')
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\WorkLog'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_worklog';
  }

}

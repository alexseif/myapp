<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

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
            return ['data-est' => $tasks->getEst(), 'data-client' => $tasks->getClient()];
          },
          'attr' => array(
            'class' => 'chosen'
          )
        ))
        ->add('name')
        ->add('duration')
        ->add('pricePerUnit', MoneyType::class)
        ->add('total', MoneyType::class)
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

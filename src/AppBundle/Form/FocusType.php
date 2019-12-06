<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FocusType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
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
          'choice_label' => 'name',
          'attr' => array(
            'class' => 'chosen',
          )
        ))
        ->add('duration')
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Focus'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_focus';
  }

}

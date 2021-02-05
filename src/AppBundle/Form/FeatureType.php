<?php

namespace AppBundle\Form;

use AppBundle\Entity\Feature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Repository\TaskListsRepository;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class FeatureType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('list', EntityType::class, [
          'class' => 'AppBundle:TaskLists',
          'query_builder' => function (TaskListsRepository $er) {
            return $er->findActiveQuery();
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
          'attr' => [
            'class' => 'chosen'
          ]
        ])
        ->add('title')
        ->add('description')
        ->add('priority')
        ->add('sort')
        ->add('rate')
        ->add('est')
        ->add('offer')
        ->add('isApproved')
        ->add('approvedAt', DateTimeType::class, array(
          'date_widget' => 'single_text',
          'time_widget' => 'single_text',
          'date_format' => 'yyyy-MM-dd',
          'required' => false
    ))
    ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Feature::class,
    ]);
  }

}

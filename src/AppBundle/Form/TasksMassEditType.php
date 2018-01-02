<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TasksMassEditType extends AbstractType
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
            -1 => 'Low',
            0 => 'Normal',
            1 => 'Important',
          ),
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('urgency', ChoiceType::class, array(
          'choices' => array(
            0 => 'Not Urgent',
            1 => 'Urgent',
          ),
          'expanded' => true,
          'label_attr' => array('class' => 'radio-inline')
        ))
        ->add('taskList', 'entity', array('class' => \AppBundle\Entity\TaskLists::class, 'choice_label' => 'name'))
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

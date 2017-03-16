<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DaysType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('deadline', 'date', array(
          'widget' => 'single_text',
          'format' => 'dd/MM/yyyy',
          'attr' => array(
            'class' => 'form-control input-inline datepicker',
            'data-provide' => 'datepicker',
            'data-date-format' => 'dd/mm/yy',
          )
        ))
        ->add('complete')
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Days'
    ));
  }

}

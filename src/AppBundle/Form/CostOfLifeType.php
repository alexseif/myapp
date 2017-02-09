<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class CostOfLifeType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('value', MoneyType::class, array(
          'currency' => ($options['data']->getCurrency() ? $options['data']->getCurrency()->getCode() : ""),
          'divisor' => 100,
          'scale' => 2,
        ))
        ->add('currency', 'entity', array(
          'class' => \AppBundle\Entity\Currency::class,
          'choice_label' => 'code'
        ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\CostOfLife'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_costoflife';
  }

}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class CostType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('name')
        ->add('value', MoneyType::class, array(
          'currency' => ($options['data']->getCurrency() ? $options['data']->getCurrency()->getCode() : ""),
          'divisor' => 100,
          'scale' => 2,
        ))
        ->add('currency', 'entity', array(
          'class' => \AppBundle\Entity\Currency::class,
          'choice_label' => 'code'
        ))
        ->add('generatedAt', 'date', array(
          'widget' => 'single_text',
          'format' => 'dd/MM/yyyy',
          'attr' => array(
            'class' => 'form-control input-inline datepicker',
            'data-provide' => 'datepicker',
            'data-date-format' => 'dd/mm/yy',
          )
        ))
        ->add('note')
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Cost'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_cost';
  }

}

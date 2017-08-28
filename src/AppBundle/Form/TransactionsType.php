<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class TransactionsType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('date', DateType::class, array(
          'widget' => 'single_text',
          'format' => 'yyyy-MM-dd',
          'required' => false,
          'attr' => array(
            'class' => 'datepicker',
            'data-provide' => 'datepicker',
            'data-date-format' => 'yyyy-MM-dd',
          )
        ))
        ->add('currency', EntityType::class, array(
          'class' => 'AppBundle:Currency',
          'choice_attr' => function($currency, $key, $index) {
            return ['data-egp' => $currency->getEgp() / 100];
          },
        ))
        ->add('EGP', MoneyType::class, array('currency' => 'EGP',
          'divisor' => 100,
          'scale' => 3,
          'label' => 'EGP'
        ))
        ->add('value', MoneyType::class, array(
          'divisor' => 100,
          'scale' => 3
        ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Transactions'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_transactions';
  }

}

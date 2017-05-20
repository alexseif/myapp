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
        ->add('value', MoneyType::class)
        ->add('currency', EntityType::class, array(
          'class' => 'AppBundle:Currency',
    ));
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

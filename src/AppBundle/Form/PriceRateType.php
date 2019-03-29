<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class PriceRateType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('unit', ChoiceType::class, [
          'choices' => [
            'Rates' => [
              'Hour' => 'Hour',
              'Day' => 'Day',
              'Week' => 'Week',
              'Month' => 'Month',
            ],
            'Fixed' => [
              'Package' => 'Package',
              'Item' => 'Item',
            ]
          ]
        ])
        ->add('pricePerUnit', MoneyType::class);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\PriceRate'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_pricerate';
  }

}

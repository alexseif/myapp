<?php

namespace App\Form;

use App\Entity\CostOfLife;
use App\Entity\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostOfLifeType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder
          ->add('name')
          ->add('value', MoneyType::class, [
            'currency' => ($options['data']->getCurrency(
            ) ? $options['data']->getCurrency()->getCode() : ''),
            'divisor' => 100,
            'scale' => 2,
          ])
          ->add('currency', EntityType::class, [
            'class' => Currency::class,
            'choice_label' => 'code',
          ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => CostOfLife::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_costoflife';
    }

}

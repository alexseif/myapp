<?php

namespace App\Form;

use App\Entity\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder->add('code')
          ->add('name')
          ->add('egp', MoneyType::class, [
            'currency' => 'EGP',
            'divisor' => 100,
            'scale' => 3,
            'label' => 'EGP',
          ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => Currency::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_currency';
    }

}

<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder->add('name')
          ->add('enabled', CheckboxType::class, [
            'required' => false,
            'label_attr' => ['class' => 'switch_box'],
          ])
          ->add('billingOption', BillingOptionsType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => Client::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_client';
    }

}

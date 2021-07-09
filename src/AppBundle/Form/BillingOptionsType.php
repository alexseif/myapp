<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingOptionsType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $days = [];
        for ($i = 1; $i <= 30; $i++) {
            $days[$i] = $i;
        }
        $builder
            ->add('hours', NumberType::class)
            ->add('hoursPer', ChoiceType::class, [
                'choices' => [
                    'day' => 'day',
                    'month' => 'month',
                    'every' => 'every',
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ]
            ])
            ->add('amount', MoneyType::class)
            ->add('amountPer', ChoiceType::class, [
                'choices' => [
                    'day' => 'day',
                    'month' => 'month',
                    'every' => 'every',
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ]
            ])
            ->add('billingOn', ChoiceType::class, [
                'choices' => $days
            ])
            //@todo calculate discounts
            ->add('discount', NumberType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_billing_options';
    }

}

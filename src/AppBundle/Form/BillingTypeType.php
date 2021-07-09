<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingTypeType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('hours', NumberType::class)
            ->add('hoursPer', ChoiceType::class, [
                'choices' => [
                    'every' => 'every',
                    'month' => 'month',
                    'day' => 'day',
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ]
            ])
            ->add('amount', MoneyType::class)
            ->add('amountPer', ChoiceType::class, [
                'choices' => [
                    'every' => 'every',
                    'month' => 'month',
                    'day' => 'day',
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ]
            ]);
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
        return 'appbundle_billing_type';
    }

}

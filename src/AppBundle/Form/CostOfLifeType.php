<?php

namespace AppBundle\Form;

use AppBundle\Entity\Currency;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('value', MoneyType::class, [
                'currency' => ($options['data']->getCurrency() ? $options['data']->getCurrency()->getCode() : ''),
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\CostOfLife',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_costoflife';
    }
}

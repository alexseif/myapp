<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ContractType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('client', EntityType::class, array(
                    'required' => false,
                    'class' => 'AppBundle:Client',
                    'choice_label' => 'name',
                    'attr' => array(
                        'class' => 'chosen',
                    ))
            )
            ->add('hoursPerDay')
            ->add('startedAt', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
            ))
            ->add('isCompleted')
            ->add('completedAt', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contract'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contract';
    }

}

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Feature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('list')
            ->add('title')
            ->add('description')
            ->add('priority')
            ->add('sort')
            ->add('rate')
            ->add('est')
            ->add('offer')
            ->add('isApproved')
            ->add('approvedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
        ]);
    }
}

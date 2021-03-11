<?php

namespace AppBundle\Form;

use AppBundle\Entity\Proposal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProposalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, array(
                    'class' => 'AppBundle:Client',
                    'choice_label' => 'name',
                    'attr' => array(
                        'class' => 'chosen',
                    ))
            )
            ->add('title')
            ->add('status')
            ->add('enabled')
            ->add('details', CollectionType::class, [
                'entry_type' => ProposalDetailsType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proposal::class,
        ]);
    }
}

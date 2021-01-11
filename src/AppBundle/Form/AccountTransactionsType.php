<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class AccountTransactionsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('account', EntityType::class, array(
                'class' => 'AppBundle:Accounts',
                'choice_label' => 'name',
                'attr' => array(
                    'class' => 'chosen',
                )
            ))
            ->add('amount', MoneyType::class)
            ->add('issuedAt', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('note');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AccountTransactions'
        ));
    }

}

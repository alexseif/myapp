<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AccountingMainFilterType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('account', EntityType::class, array(
          'placeholder' => 'Choose an Account',
          'class' => \AppBundle\Entity\Accounts::class,
          'query_builder' => function (\AppBundle\Repository\AccountsRepository $er) {
            return $er->findAllwithJoin();
          },
          'group_by' => function($account) {
            return $account->getClient();
          },
          'choice_label' => 'name',
          'attr' => array(
            'class' => 'chosen',
          ))
        )
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'csrf_protection' => false,
    ));
  }

}

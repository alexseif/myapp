<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AccountingMainFilterType extends AbstractType
{

  protected $router;

  function __construct(\Symfony\Component\Routing\RouterInterface $router)
  {
    $this->router = $router;
  }

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('account', EntityType::class, [
          'placeholder' => 'Choose an Account',
          'class' => \AppBundle\Entity\Accounts::class,
          'query_builder' => function (\AppBundle\Repository\AccountsRepository $er) {
            return $er->findAllwithJoin();
          },
          'choice_value' => function (?\AppBundle\Entity\Accounts $entity) {
            return ($entity) ? $this->router->generate('accounting_account_page', ['id' => $entity->getId()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL) : '';
          },
          'group_by' => function($account) {
            return $account->getClient();
          },
          'choice_label' => 'name',
          'attr' => [
            'class' => 'chosen',
          ]]
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

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Accounts;
use AppBundle\Repository\AccountsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AccountingMainFilterType extends AbstractType
{

    protected $router;

    function __construct(RouterInterface $router)
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
                    'class' => Accounts::class,
                    'query_builder' => function (AccountsRepository $er) {
                        return $er->findAllWithJoin();
                    },
                    'choice_value' => function (?Accounts $entity) {
                        return ($entity) ? $this->router->generate('accounting_account_page', ['id' => $entity->getId()], UrlGeneratorInterface::ABSOLUTE_URL) : '';
                    },
                    'group_by' => function ($account) {
                        return $account->getClient();
                    },
                    'choice_label' => 'name',
                    'attr' => [
                        'class' => 'chosen',
                    ]]
            );
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

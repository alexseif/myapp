<?php

namespace AppBundle\Form;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\TaskLists;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskListsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('account', EntityType::class, [
                    'required' => false,
                    'class' => Accounts::class,
                    'choice_label' => 'name',
                    'attr' => [
                        'class' => 'chosen',
                    ], ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskLists::class,
        ]);
    }
}

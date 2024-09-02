<?php

namespace App\Form;

use App\Entity\Accounts;
use App\Entity\TaskLists;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskListsType extends AbstractType
{

    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder
          ->add('name')
          ->add('account', EntityType::class, [
              'required' => false,
              'class' => Accounts::class,
              'choice_label' => 'name',
              'attr' => [
                'class' => 'chosen',
              ],
            ]
          );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => TaskLists::class,
        ]);
    }

}

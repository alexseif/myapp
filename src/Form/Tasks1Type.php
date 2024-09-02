<?php

namespace App\Form;

use App\Entity\Tasks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Tasks1Type extends AbstractType
{

    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder
          ->add('task')
          ->add('order')
          ->add('priority')
          ->add('urgency')
          ->add('duration')
          ->add('est')
          ->add('eta')
          ->add('completed')
          ->add('completedAt')
          ->add('workLoggable')
          ->add('createdAt')
          ->add('updatedAt')
          ->add('taskList')
          ->add('workLog');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => Tasks::class,
        ]);
    }

}

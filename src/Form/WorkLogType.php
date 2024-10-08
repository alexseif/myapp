<?php

namespace App\Form;

use App\Entity\Tasks;
use App\Entity\WorkLog;
use App\Repository\TasksRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkLogType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder
          ->add('task', EntityType::class, [
            'class' => Tasks::class,
            'query_builder' => function (TasksRepository $er) {
                return $er->createQueryBuilder('t')
                  ->select('t, tl, a, c, r, wl, s')
                  ->leftJoin('t.taskList', 'tl')
                  ->leftJoin('tl.account', 'a')
                  ->leftJoin('a.client', 'c')
                  ->leftJoin('t.workLog', 'wl')
                  ->leftJoin('c.rates', 'r')
                  ->leftJoin('t.schedule', 's')
                  ->where('t.completed = 1')
                  ->andWhere('tl.status <> \'archive\'');
            },
            'group_by' => function ($tasks) {
                return $tasks->getTaskList()->getName();
            },
            'choice_attr' => function ($tasks) {
                return [
                  'data-duration' => $tasks->getDuration(),
                  'data-client' => $tasks->getClient(),
                ];
            },
            'attr' => [
              'class' => 'chosen',
            ],
          ])
          ->add('name')
          ->add('duration')
          ->add('pricePerUnit', MoneyType::class)
          ->add('total', MoneyType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => WorkLog::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_worklog';
    }

}

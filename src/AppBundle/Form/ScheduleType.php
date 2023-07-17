<?php

namespace AppBundle\Form;

use AppBundle\Entity\Schedule;
use AppBundle\Entity\Tasks;
use AppBundle\Repository\TasksRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', EntityType::class, [
                'class' => Tasks::class,
                'query_builder' => function (TasksRepository $tasksRepository) {
                    return $tasksRepository->createQueryBuilder('t')
                        ->select('t, tl, a, c, wl, s')
                        ->leftJoin(TasksRepository::TASKLIST, 'tl')
                        ->leftJoin(TasksRepository::ACCOUNT, 'a')
                        ->leftJoin(TasksRepository::CLIENT, 'c')
                        ->leftJoin(TasksRepository::WORKLOG, 'wl')
                        ->leftJoin('t.schedule', 's')
                        ->where(TasksRepository::NOT_COMPLETED)
                        ;
                },
                'group_by' => function ($task) {
                    return $task->getTaskList()->getName();
                },
                'attr' => [
                    'class' => 'chosen',
                ],
            ])
            ->add('est')
            ->add('eta', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}

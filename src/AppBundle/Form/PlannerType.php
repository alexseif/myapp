<?php

namespace AppBundle\Form;

use AppBundle\Entity\Days;
use AppBundle\Entity\Objective;
use AppBundle\Entity\Planner;
use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Thing;
use AppBundle\Repository\TaskListsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('objectives', EntityType::class, [
                'required' => false,
                'class' => Objective::class,
                'multiple' => true,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'chosen',
                ]
            ])
            ->add('things', EntityType::class, [
                'required' => false,
                'class' => Thing::class,
                'multiple' => true,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'chosen',
                ]
            ])
            ->add('tasklists', EntityType::class, array(
                'required' => false,
                'class' => TaskLists::class,
                'multiple' => true,
                'query_builder' => function (TaskListsRepository $er) {
                    return $er->createQueryBuilder('tl')
                        ->where('tl.status <> \'archive\'');
                },
                'group_by' => function ($taskList) {
                    if ($taskList->getAccount()) {
                        if ($taskList->getAccount()->getClient()) {
                            return $taskList->getAccount()->getClient()->getName();
                        }
                        return $taskList->getAccount()->getName();
                    }
                    return "N/A";
                },
                'choice_label' => 'name',
                'attr' => array(
                    'class' => 'chosen',
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planner::class,
        ]);
    }
}

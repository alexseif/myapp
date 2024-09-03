<?php

namespace App\Controller;

use App\Entity\Accounts;
use App\Entity\Tasks;
use App\Repository\AccountsRepository;
use App\Repository\TasksRepository;
use App\Service\RateCalculator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Experiments controller.
 *
 * @Route("/experiments")
 */
class ExperimentsController extends AbstractController
{

    /**
     * @Route("/", name="experiments_index")
     */
    public function indexAction(): Response
    {
        $experiments = [
          'Reports' => 'reports_index',
          'Tasks' => 'experiment_tasks',
          'Accounts' => 'experiment_accounts',
        ];

        return $this->render('Experiments/index.html.twig', [
          'experiments' => $experiments,
        ]);
    }

    /**
     * @Route("/tasks", name="experiment_tasks", methods={"GET", "POST"})
     */
    public function listTasksAction(
      Request $request,
      RateCalculator $rateCalculator,
      TasksRepository $tasksRepository
    ): Response {
        $data = [
          'experiments' => ['Rate Task' => 'experiment_tasks'],
          'tasks' => $tasksRepository->findAll(),
        ];
        $form = $this->createFormBuilder($data)
          ->add('experiments', ChoiceType::class, [
            'mapped' => false,
            'choices' => $data['experiments'],
            'placeholder' => 'Select an experiment',
            'attr' => [
              'class' => 'chosen',
            ],
          ])
          ->add('tasks', EntityType::class, [
            'class' => Tasks::class,
            'placeholder' => 'Select a task',
            'attr' => [
              'class' => 'chosen',
            ],
          ])
          ->add('Yalla', SubmitType::class, [
            'attr' => [
              'class' => 'btn btn-success float-right',
            ],
          ])
          ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $data['result']['experiment'] = $formData['experiments'];
            $data['result']['task'] = $formData['tasks'];

            $data['result']['price'] = $rateCalculator->task(
              $formData['tasks']
            );
        }

        return $this->render('Experiments/tasks.html.twig', [
          'form' => $form->createView(),
          'data' => $data,
        ]);
    }

    /**
     * @Route("/accounts", name="experiment_accounts", methods={"GET", "POST"})
     */
    public function accountsAction(
      Request $request,
      RateCalculator $rateCalculator,
      TasksRepository $tasksRepository
    ): Response {
        $data = [
          'experiments' => ['Account' => 'experiment_accounts'],
        ];
        $form = $this->createFormBuilder($data)
          ->add('experiments', ChoiceType::class, [
            'mapped' => false,
            'choices' => $data['experiments'],
            'placeholder' => 'Select an experiment',
            'attr' => [
              'class' => 'chosen',
            ],
          ])
          ->add('account', EntityType::class, [
            'class' => Accounts::class,
            'placeholder' => 'Select an Account',
            'query_builder' => function (AccountsRepository $ar) {
                return $ar->createQueryBuilder('a')
                  ->select('a, c')
                  ->leftJoin('a.client', 'c');
            },
            'group_by' => function ($account) {
                if ($account->getClient()) {
                    return $account->getClient()->getName();
                }

                return 'N/A';
            },
            'attr' => [
              'class' => 'chosen',
            ],
          ])
          ->add('Yalla', SubmitType::class, [
            'attr' => [
              'class' => 'btn btn-success float-right',
            ],
          ])
          ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $data['result']['experiment'] = $formData['experiments'];
            $data['result']['account'] = $formData['account'];
            $data['result']['tasks'] = $tasksRepository->findByAccountNoWorklog(
              $data['result']['account']
            );
            $data['result']['price'] = $rateCalculator->tasks(
              $data['result']['tasks']
            );
        }

        return $this->render('Experiments/accounts.html.twig', [
          'form' => $form->createView(),
          'data' => $data,
        ]);
    }

}

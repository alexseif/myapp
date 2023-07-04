<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\Tasks;
use AppBundle\Repository\AccountsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Experiments controller.
 *
 * @Route("/experiments")
 */
class ExperimentsController extends Controller
{
    /**
     * @Route("/", name="experiments_index")
     */
    public function indexAction()
    {
        $experiments = [
            'Reports' => 'reports_index',
            'Tasks' => 'experiment_tasks',
            'Accounts' => 'experiment_accounts',
        ];

        return $this->render('AppBundle:Experiments:index.html.twig', [
            'experiments' => $experiments,
        ]);
    }

    /**
     * @Route("/tasks", name="experiment_tasks", methods={"GET", "POST"})
     */
    public function listTasksAction(Request $request)
    {
        $data = [
            'experiments' => ['Rate Task' => 'experiment_tasks'],
            'tasks' => $this->getDoctrine()->getRepository(Tasks::class)->findAll(),
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
                'class' => 'AppBundle:Tasks',
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
            $rateCaltulcator = $this->get('myapp.rate.calculator');

            $data['result']['experiment'] = $formData['experiments'];
            $data['result']['task'] = $formData['tasks'];

            $data['result']['price'] = $rateCaltulcator->task($formData['tasks']);
        }

        return $this->render('AppBundle:Experiments:tasks.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }

    /**
     * @Route("/accounts", name="experiment_accounts", methods={"GET", "POST"})
     */
    public function accountsAction(Request $request)
    {
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
            $rateCalculator = $this->get('myapp.rate.calculator');

            $data['result']['experiment'] = $formData['experiments'];
            $data['result']['account'] = $formData['account'];
            $data['result']['tasks'] = $this->getDoctrine()->getRepository(Tasks::class)
                ->findByAccountNoWorklog($data['result']['account']);
            $data['result']['price'] = $rateCalculator->tasks($data['result']['tasks']);
        }

        return $this->render('AppBundle:Experiments:accounts.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }
}

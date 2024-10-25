<?php

namespace App\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlanController extends AbstractController
{
    #[Route('/plan', name: 'app_plan')]
    public function index(): Response
    {
        $plan = [
            'Task' => [
                'CRUD' => [
                    'name',
                    'order',
                    'priority',
                    'urgency',
                    'duration',
                    'est',
                    'eta',
                    'completed',
                    'completedAt',
                    'Tasklist::class',
                    'WorkLog',
                    'WorkLoggable',
                    'createdAt',
                    'updatedAt'
                ],
            ],
            'Task List' => [
                'CRUD' => [
                    'name',
                    'Account::class',
                    'status',
                    'Task::class',
                    'createdAt',
                    'updatedAt'
                ]
            ],
            'Account' => [
                'CRUD' => [
                    'name',
                    'conceal',
                    'Transaction::class',
                    'Client::class',
                    'Tasklist::class',
                    'balance',
                    'createdAt',
                    'updatedAt'
                ]
            ],
            'Client' => [
                'CRUD' => [
                    'name',
                    'Account::class',
                    'contracts',
                    'rates',
                    'billingOption',
                    'createdAt',
                    'updatedAt'
                ]
            ],
            'Transaction' => [
                'CRUD' => [
                    'amount',
                    'note',
                    'issuedAt',
                    'Account::class',
                    'createdAt',
                    'updatedAt'
                ]
            ]
        ];
        return $this->render('plan/index.html.twig', [
            'plan' => $plan
        ]);
    }
}

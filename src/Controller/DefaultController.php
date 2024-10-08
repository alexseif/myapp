<?php

namespace App\Controller;

use App\Entity\TaskLists;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{

    #[Route("/lists", name:"lists_view")]
    public function listsAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;
        $today = new DateTime();

        $lists = $em->getRepository(TaskLists::class)->findBy(
          ['status' => 'start']
        );

        return $this->render('default/lists.html.twig', [
          'today' => $today,
          'lists' => $lists,
        ]);
    }

    #[Route("/lists/{id}/modal", name:"list_show_modal", methods:["GET"])]
    public function listModalAction(TaskLists $taskList
    ): Response {
        $tasks = $taskList->getTasks(false);
        $random = random_int(0, $tasks->count() - 1);

        return $this->render('tasks/show_modal.html.twig', [
          'task' => $tasks->get($random),
        ]);
    }

    #[Route("/getBottomBarDetails", name:"get_bottom_bar_details", methods:["GET"])]
    public function getBottomBarDetails(
    ): Response
    {
        return $this->render('bottom-bar-details.html.twig');
    }

}

<?php

namespace App\Controller;

use App\Entity\Accounts;
use App\Entity\Client;
use App\Entity\Days;
use App\Entity\Notes;
use App\Entity\TaskLists;
use App\Entity\Tasks;
use App\Form\ManagementSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Management controller.
 *
 * @Route("/management")
 */
class ManagementController extends AbstractController
{
    /**
     * @Route("/", name="management_index")
     */
    public function indexAction(Request $request, EntityManagerInterface $entityManager)
    {
        $em = $entityManager;

        $tasks = $em->getRepository(Tasks::class)->getIncomplete();
        $form = $this->createForm(ManagementSearchType::class, $request->get('management_search'), [
            'method' => 'GET',
            'action' => $this->generateUrl('management_search_page'),
        ]);

        return $this->render('management/index.html.twig', [
            'tasks' => $tasks,
            'management_search_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/priority", name="management_priority")
     */
    public function priorityAction(EntityManagerInterface $entityManager)
    {
        $em = $entityManager;

        $tasks = $em->getRepository(Tasks::class)->getIncomplete();

        return $this->render('management/priority.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Search all entities.
     *
     * @Route("/search", name="management_search_page", methods={"GET"})
     */
    public function searchAction(Request $request, EntityManagerInterface $entityManager)
    {
        $em = $entityManager;
        $form = $this->createForm(\Form\ManagementSearchType::class, $request->get('management_search'), [
            'method' => 'GET',
            'action' => $this->generateUrl('management_search_page'),
        ]);
        $results = [];
        $filters = [];
        if ($request->query->has($form->getName())) {
            $filters = $request->get('management_search');
            $results['days'] = $em->getRepository(Days::class)->search($filters['search']);
            $results['clients'] = $em->getRepository(Client::class)->search($filters['search']);
            $results['accounts'] = $em->getRepository(Accounts::class)->search($filters['search']);
            $results['taskLists'] = $em->getRepository(TaskLists::class)->search($filters['search']);
            $results['tasks'] = $em->getRepository(Tasks::class)->search($filters['search']);
            $results['notes'] = $em->getRepository(Notes::class)->search($filters['search']);
        }

        return $this->render('management/search.html.twig', [
            'filters' => $filters,
            'results' => $results,
            'management_search_form' => $form->createView(),
        ]);
    }
}

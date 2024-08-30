<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\Client;
use AppBundle\Entity\Days;
use AppBundle\Entity\Notes;
use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository(Tasks::class)->getIncomplete();
        $form = $this->createForm(\AppBundle\Form\ManagementSearchType::class, $request->get('management_search'), [
            'method' => 'GET',
            'action' => $this->generateUrl('management_search_page'),
        ]);

        return $this->render('@App/management/index.html.twig', [
            'tasks' => $tasks,
            'management_search_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/priority", name="management_priority")
     */
    public function priorityAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository(Tasks::class)->getIncomplete();

        return $this->render('@App/management/priority.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Search all entities.
     *
     * @Route("/search", name="management_search_page", methods={"GET"})
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(\AppBundle\Form\ManagementSearchType::class, $request->get('management_search'), [
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

        return $this->render('@App/management/search.html.twig', [
            'filters' => $filters,
            'results' => $results,
            'management_search_form' => $form->createView(),
        ]);
    }
}

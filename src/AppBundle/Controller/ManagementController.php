<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Tasks;

/**
 * Management controller.
 *
 * @Route("/management")
 */
class ManagementController extends Controller
{

  /**
   * @Route("/", name="management_index")
   * @Template("AppBundle:management:index.html.twig")
   */
  public function indexAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->getIncopmleteTasks();
    $form = $this->createForm(\AppBundle\Form\ManagementSearchType::class, $request->get('management_search'), array(
      'method' => 'GET',
      'action' => $this->generateUrl('management_search_page')
    ));
    return array(
      'tasks' => $tasks,
      'management_search_form' => $form->createView()
    );
  }

  /**
   * @Route("/priority", name="management_priority")
   * @Template("AppBundle:Management:priority.html.twig")
   */
  public function priorityAction()
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->getIncomplete();

    return array(
      'tasks' => $tasks,
    );
  }

  /**
   * Search all entities.
   *
   * @Route("/search", name="management_search_page", methods={"GET"})
   * @Template("AppBundle:Management:search.html.twig")
   */
  public function searchAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $form = $this->createForm(\AppBundle\Form\ManagementSearchType::class, $request->get('management_search'), array(
      'method' => 'GET',
      'action' => $this->generateUrl('management_search_page')
    ));
    $results = array();
    $filters = array();
    if ($request->query->has($form->getName())) {
      $filters = $request->get('management_search');
      $results['days'] = $em->getRepository('AppBundle:Days')->search($filters['search']);
      $results['clients'] = $em->getRepository('AppBundle:Client')->search($filters['search']);
      $results['accounts'] = $em->getRepository('AppBundle:Accounts')->search($filters['search']);
      $results['taskLists'] = $em->getRepository('AppBundle:TaskLists')->search($filters['search']);
      $results['tasks'] = $em->getRepository('AppBundle:Tasks')->search($filters['search']);
      $results['notes'] = $em->getRepository('AppBundle:Notes')->search($filters['search']);
    }

    return array(
      'filters' => $filters,
      'results' => $results,
      'management_search_form' => $form->createView(),
    );
  }

}

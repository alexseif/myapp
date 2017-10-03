<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Planner;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Planner controller.
 *
 * @Route("planner")
 */
class PlannerController extends Controller
{

  /**
   * Lists all planner entities.
   *
   * @Route("/", name="planner_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $planners = $em->getRepository('AppBundle:Planner')->findAll();

    return $this->render('planner/index.html.twig', array(
          'planners' => $planners,
    ));
  }

  /**
   * Creates a new planner entity.
   *
   * @Route("/new", name="planner_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $planner = new Planner();
    $form = $this->createForm('AppBundle\Form\PlannerType', $planner);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      if (!$this->processPlannerTasks($planner)) {
        $em->persist($planner);
        $em->flush($planner);

        return $this->redirectToRoute('planner_show', array('id' => $planner->getId()));
      }
    }

    return $this->render('planner/new.html.twig', array(
          'planner' => $planner,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a planner entity.
   *
   * @Route("/{id}", name="planner_show")
   * @Method("GET")
   */
  public function showAction(Planner $planner)
  {
    $deleteForm = $this->createDeleteForm($planner);

    return $this->render('planner/show.html.twig', array(
          'planner' => $planner,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing planner entity.
   *
   * @Route("/{id}/edit", name="planner_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Planner $planner)
  {
    $deleteForm = $this->createDeleteForm($planner);
    $editForm = $this->createForm('AppBundle\Form\PlannerType', $planner);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      if (!$this->processPlannerTasks($planner)) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('planner_edit', array('id' => $planner->getId()));
      }
    }

    return $this->render('planner/edit.html.twig', array(
          'planner' => $planner,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  public function processPlannerTasks($planner)
  {
    $duplicates = new \Doctrine\Common\Collections\ArrayCollection();
    $duplicatesFound = false;
    foreach ($planner->getTasks() as $task) {

      //Check doesn't exist in another planner
      $em = $this->getDoctrine()->getManager();

      $foundPlanner = $em->getRepository('AppBundle:Planner')->findPlannerByTask($task);

      if ($foundPlanner) {
        $duplicatesFound = true;
        $duplicates->add($task);
      }
    }

    if ($duplicatesFound) {
      $this->addFlash('error', 'Fuck there are duplicates what do you want to do?');
      return true;
    }
    return false;
  }

  /**
   * Deletes a planner entity.
   *
   * @Route("/{id}", name="planner_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Planner $planner)
  {
    $form = $this->createDeleteForm($planner);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($planner);
      $em->flush($planner);
    }

    return $this->redirectToRoute('planner_index');
  }

  /**
   * Creates a form to delete a planner entity.
   *
   * @param Planner $planner The planner entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Planner $planner)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('planner_delete', array('id' => $planner->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

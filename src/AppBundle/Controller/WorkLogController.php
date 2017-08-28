<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Worklog controller.
 *
 * @Route("worklog")
 */
class WorkLogController extends Controller
{

  /**
   * Lists all workLog entities.
   *
   * @Route("/", name="worklog_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $workLogs = $em->getRepository('AppBundle:WorkLog')->findAll();

    return $this->render('worklog/index.html.twig', array(
          'workLogs' => $workLogs,
    ));
  }

  /**
   * Creates a new workLog entity.
   *
   * @Route("/new", name="worklog_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();


    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);

    
    $workLog = new Worklog();
    $workLog->setPricePerUnit($costOfLife->getHourly());
    $form = $this->createForm('AppBundle\Form\WorkLogType', $workLog);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
//      $em = $this->getDoctrine()->getManager();
      $em->persist($workLog);
      $em->flush($workLog);

      return $this->redirectToRoute('worklog_show', array('id' => $workLog->getId()));
    }

    return $this->render('worklog/new.html.twig', array(
          'workLog' => $workLog,
          'costOfLife' => $costOfLife,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a workLog entity.
   *
   * @Route("/{id}", name="worklog_show")
   * @Method("GET")
   */
  public function showAction(WorkLog $workLog)
  {
    $deleteForm = $this->createDeleteForm($workLog);

    return $this->render('worklog/show.html.twig', array(
          'workLog' => $workLog,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing workLog entity.
   *
   * @Route("/{id}/edit", name="worklog_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, WorkLog $workLog)
  {
    $deleteForm = $this->createDeleteForm($workLog);
    $editForm = $this->createForm('AppBundle\Form\WorkLogType', $workLog);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('worklog_edit', array('id' => $workLog->getId()));
    }

    return $this->render('worklog/edit.html.twig', array(
          'workLog' => $workLog,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a workLog entity.
   *
   * @Route("/{id}", name="worklog_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, WorkLog $workLog)
  {
    $form = $this->createDeleteForm($workLog);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($workLog);
      $em->flush($workLog);
    }

    return $this->redirectToRoute('worklog_index');
  }

  /**
   * Creates a form to delete a workLog entity.
   *
   * @param WorkLog $workLog The workLog entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(WorkLog $workLog)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('worklog_delete', array('id' => $workLog->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Balance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Balance controller.
 *
 * @Route("balance")
 */
class BalanceController extends Controller
{

  /**
   * Lists all balance entities.
   *
   * @Route("/", name="balance_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $balances = $em->getRepository('AppBundle:Balance')->findAll();

    return $this->render('balance/index.html.twig', array(
          'balances' => $balances,
    ));
  }

  /**
   * Creates a new balance entity.
   *
   * @Route("/new", name="balance_new", methods={"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $balance = new Balance();
    $form = $this->createForm('AppBundle\Form\BalanceType', $balance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($balance);
      $em->flush();

      return $this->redirectToRoute('balance_show', array('id' => $balance->getId()));
    }

    return $this->render('balance/new.html.twig', array(
          'balance' => $balance,
          'balance_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a balance entity.
   *
   * @Route("/{id}", name="balance_show", methods={"GET"})
   */
  public function showAction(Balance $balance)
  {
    $deleteForm = $this->createDeleteForm($balance);

    return $this->render('balance/show.html.twig', array(
          'balance' => $balance,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing balance entity.
   *
   * @Route("/{id}/edit", name="balance_edit", methods={"GET", "POST"})
   */
  public function editAction(Request $request, Balance $balance)
  {
    $deleteForm = $this->createDeleteForm($balance);
    $editForm = $this->createForm('AppBundle\Form\BalanceType', $balance);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('balance_edit', array('id' => $balance->getId()));
    }

    return $this->render('balance/edit.html.twig', array(
          'balance' => $balance,
          'balance_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a balance entity.
   *
   * @Route("/{id}", name="balance_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, Balance $balance)
  {
    $form = $this->createDeleteForm($balance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($balance);
      $em->flush();
    }

    return $this->redirectToRoute('balance_index');
  }

  /**
   * Creates a form to delete a balance entity.
   *
   * @param Balance $balance The balance entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Balance $balance)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('balance_delete', array('id' => $balance->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

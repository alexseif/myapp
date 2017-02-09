<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Accounts;
use AppBundle\Entity\Cost;
use AppBundle\Form\AccountsType;
use AppBundle\Form\CostType;

/**
 * Accounts controller.
 *
 * @Route("/accounts")
 */
class AccountsCostController extends Controller
{

  /**
   * Lists all Accounts entities.
   *
   * @Route("/{id}/cost", name="account_costs_index")
   * @Method("GET")
   */
  public function indexAction(Accounts $account)
  {
    $em = $this->getDoctrine()->getManager();

    return $this->render('accounts/account_costs.html.twig', array(
          'account' => $account,
    ));
  }

  /**
   * Creates a new Accounts entity.
   *
   * @Route("/{id}/cost/new", name="account_costs_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request, Accounts $account)
  {
    $cost = new Cost();
    $cost->setName($account->getName());
    $form = $this->createForm('AppBundle\Form\CostType', $cost);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $account->addCost($cost);
      $em->persist($cost);
      $em->flush();

//      return $this->redirectToRoute('accounts_show', array('id' => $account->getId()));
    }

    return $this->render('accounts/account_costs.html.twig', array(
          'cost' => $cost,
          'account' => $account,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a Accounts entity.
   *
   * @Route("/{id}", name="account_cost_show")
   * @Method("GET")
   */
  public function showAction(Accounts $account)
  {
    $deleteForm = $this->createDeleteForm($account);

    return $this->render('accounts/show.html.twig', array(
          'account' => $account,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Accounts entity.
   *
   * @Route("/{id}/edit", name="account_cost_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Accounts $account)
  {
    $deleteForm = $this->createDeleteForm($account);
    $editForm = $this->createForm('AppBundle\Form\AccountsType', $account);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($account);
      $em->flush();

      return $this->redirectToRoute('accounts_edit', array('id' => $account->getId()));
    }

    return $this->render('accounts/edit.html.twig', array(
          'account' => $account,
          'account_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a Accounts entity.
   *
   * @Route("/{id}", name="account_cost_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Accounts $account)
  {
    $form = $this->createDeleteForm($account);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($account);
      $em->flush();
    }

    return $this->redirectToRoute('accounts_index');
  }

  /**
   * Creates a form to delete a Accounts entity.
   *
   * @param Accounts $account The Accounts entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Accounts $account)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('accounts_delete', array('id' => $account->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

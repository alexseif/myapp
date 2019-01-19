<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\AccountTransactions;
use AppBundle\Form\AccountTransactionsType;

/**
 * AccountTransactions controller.
 *
 * @Route("/accounttransactions")
 */
class AccountTransactionsController extends Controller
{

  /**
   * Lists all AccountTransactions entities.
   *
   * @Route("/", name="accounttransactions_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $accountTransactions = $em->getRepository('AppBundle:AccountTransactions')->findAll();

    return $this->render('accounttransactions/index.html.twig', array(
          'accountTransactions' => $accountTransactions,
    ));
  }

  /**
   * Creates a new AccountTransactions entity.
   *
   * @Route("/new", name="accounttransactions_new", methods={"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $accountTransaction = new AccountTransactions();
    if (!empty($request->get("account"))) {
      $account = $em->getRepository('AppBundle:Accounts')->find($request->get("account"));
      $accountTransaction->setAccount($account);
    }
    $form = $this->createForm('AppBundle\Form\AccountTransactionsType', $accountTransaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($accountTransaction);
      $em->flush();

      return $this->redirectToRoute('accounttransactions_show', array('id' => $accountTransaction->getId()));
    }

    return $this->render('accounttransactions/new.html.twig', array(
          'accountTransaction' => $accountTransaction,
          'transaction_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a AccountTransactions entity.
   *
   * @Route("/{id}", name="accounttransactions_show", methods={"GET"})
   */
  public function showAction(AccountTransactions $accountTransaction)
  {
    $deleteForm = $this->createDeleteForm($accountTransaction);

    return $this->render('accounttransactions/show.html.twig', array(
          'accountTransaction' => $accountTransaction,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing AccountTransactions entity.
   *
   * @Route("/{id}/edit", name="accounttransactions_edit", methods={"GET", "POST"})
   */
  public function editAction(Request $request, AccountTransactions $accountTransaction)
  {

    $deleteForm = $this->createDeleteForm($accountTransaction);
    $editForm = $this->createForm('AppBundle\Form\AccountTransactionsType', $accountTransaction);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($accountTransaction);
      $em->flush();

      return $this->redirectToRoute('accounttransactions_edit', array('id' => $accountTransaction->getId()));
    }

    return $this->render('accounttransactions/edit.html.twig', array(
          'accountTransaction' => $accountTransaction,
          'transaction_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a AccountTransactions entity.
   *
   * @Route("/{id}", name="accounttransactions_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, AccountTransactions $accountTransaction)
  {
    $form = $this->createDeleteForm($accountTransaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($accountTransaction);
      $em->flush();
    }

    return $this->redirectToRoute('accounttransactions_index');
  }

  /**
   * Creates a form to delete a AccountTransactions entity.
   *
   * @param AccountTransactions $accountTransaction The AccountTransactions entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(AccountTransactions $accountTransaction)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('accounttransactions_delete', array('id' => $accountTransaction->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transactions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction controller.
 *
 * @Route("transactions")
 */
class TransactionsController extends Controller
{

  /**
   * Lists all transaction entities.
   *
   * @Route("/", name="transactions_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $transactions = $em->getRepository('AppBundle:Transactions')->findBy(array(), array("date" => "ASC"));
    $currency = $this->get('myApp.currency')->get();

    $balance = 0;
    $today = new \DateTime("NOW");
    foreach ($transactions as $key => $transaction) {
      $diff = $today->diff($transaction->getDate());
      $transaction->diff = $diff;
      $transaction->past = (0 == $transaction->diff->days);
      $balance -= $transaction->getValue() / $currency[$transaction->getCurrency()->getCode()];
      $transaction->balance = $balance;
    }

    return $this->render('transactions/index.html.twig', array(
          'transactions' => $transactions,
    ));
  }

  /**
   * Lists all transaction entities.
   *
   * @Route("/process", name="transactions_process")
   * @Method("GET")
   */
  public function process()
  {
    $em = $this->getDoctrine()->getManager();
    $transactionsRepo = $em->getRepository('AppBundle:Transactions');
    $lastDate = $transactionsRepo->findLast();
    $lastDailyDate = $transactionsRepo->findLastDaily();

    if ("Daily" != $lastDate->getName()) {

      $avg = $transactionsRepo->findDailyAverage()['value'];
      $egp = $this->get('myApp.currency')->getEgp();

      $today = new \DateTime("NOW");
      $diff = $lastDate->getDate()->diff($lastDailyDate->getDate())->days;
      $save = false;

      for ($i = 0; $i < $diff; $i++) {
        $today = new \DateTime("NOW");
        $txn = new \AppBundle\Entity\Transactions();
        $txn->setCurrency($egp);
        $txn->setDate($today->add(new \DateInterval('P' . $i . 'D')));
        $txn->setName("Daily");
        $txn->setValue($avg);
        $em->persist($txn);
        $save = true;
      }
      if ($save) {
        $em->flush($txn);
      }
    }
    return $this->redirectToRoute('transactions_index');
  }

  /**
   * Creates a new transaction entity.
   *
   * @Route("/new", name="transactions_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $transaction = new Transactions();
    $form = $this->createForm('AppBundle\Form\TransactionsType', $transaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($transaction);
      $em->flush($transaction);

      return $this->redirectToRoute('transactions_show', array('id' => $transaction->getId()));
    }

    return $this->render('transactions/new.html.twig', array(
          'transaction' => $transaction,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a transaction entity.
   *
   * @Route("/{id}", name="transactions_show")
   * @Method("GET")
   */
  public function showAction(Transactions $transaction)
  {
    $deleteForm = $this->createDeleteForm($transaction);

    return $this->render('transactions/show.html.twig', array(
          'transaction' => $transaction,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing transaction entity.
   *
   * @Route("/{id}/edit", name="transactions_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Transactions $transaction)
  {
    $deleteForm = $this->createDeleteForm($transaction);
    $editForm = $this->createForm('AppBundle\Form\TransactionsType', $transaction);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('transactions_show', array('id' => $transaction->getId()));
    }

    return $this->render('transactions/edit.html.twig', array(
          'transaction' => $transaction,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a transaction entity.
   *
   * @Route("/{id}", name="transactions_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Transactions $transaction)
  {
    $form = $this->createDeleteForm($transaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($transaction);
      $em->flush($transaction);
    }

    return $this->redirectToRoute('transactions_index');
  }

  /**
   * Creates a form to delete a transaction entity.
   *
   * @param Transactions $transaction The transaction entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Transactions $transaction)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('transactions_delete', array('id' => $transaction->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

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
    return $this->render('transactions/index.html.twig', array(
          'transactions' => $this->generateBalance(),
    ));
  }

  /**
   * Lists all transaction entities.
   *
   * @Route("/permute", name="transactions_permute")
   * @Method("GET")
   */
  public function permuteAction()
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
        $txn->setEgp($avg);
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
   * Lists all transaction entities.
   *
   * @Route("/fix", name="transactions_fix")
   * @Method("GET")
   */
  public function fixAction()
  {
    $em = $this->getDoctrine()->getManager();

    $transactions = $em->getRepository('AppBundle:Transactions')->findBy(array(), array("date" => "ASC"));
    $currency = $this->get('myApp.currency')->get();
    foreach ($transactions as $key => $transaction) {
      $transaction->setValue($transaction->getValue() * 100);
      if (!$transaction->getEgp()) {
        $transaction->setEgp($transaction->getValue() / $currency[$transaction->getCurrency()->getCode()]);
      }
    }
    $em->flush();


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
    if ($request->get('calibrate')) {
      $balance = $this->generateTodaysBalance();
      $transaction->setName('Calibration');
      $transaction->setEgp($balance);
      $transaction->setValue($balance);
    }

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

  /**
   * 
   * @return float
   */
  public function generateTodaysBalance()
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime("NOW");

    $transactions = $em->getRepository('AppBundle:Transactions')->createQueryBuilder('tns')
        ->select('tns')
        ->where('tns.date <= :today')
        ->setParameter('today', $today->format('Y-m-d'))
        ->orderBy('tns.date', 'ASC')
        ->getQuery()
        ->getResult();
    $currency = $this->get('myApp.currency')->get();
    $balance = 0;
    foreach ($transactions as $key => $transaction) {
      $diff = $today->diff($transaction->getDate());
      $transaction->diff = $diff;
      $transaction->past = (0 == $transaction->diff->days);
      $balance -= $transaction->getEgp();
      $transaction->balance = $balance;
    }

    return $balance;
  }

  /**
   * 
   * @return array
   */
  public function generateBalance()
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
      $balance -= $transaction->getEgp() / 100;
      $transaction->balance = $balance;
    }
    $em->flush();

    return $transactions;
  }

}

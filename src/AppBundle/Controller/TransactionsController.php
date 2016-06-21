<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Transactions;
use AppBundle\Form\TransactionsType;

/**
 * Transactions controller.
 *
 * @Route("/transactions")
 */
class TransactionsController extends Controller
{

    /**
     * Lists all Transactions entities.
     *
     * @Route("/", name="transactions_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('AppBundle:Transactions')->findAll();

        return $this->render('transactions/index.html.twig', array(
                    'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new Transactions entity.
     *
     * @Route("/newExpense", name="new_expense")
     * @Method({"GET", "POST"})
     */
    public function newExpenseAction(Request $request)
    {
        $transaction = new Transactions();
        $form = $this->createForm('AppBundle\Form\TransactionsType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->makeExpense();
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_show', array('id' => $transaction->getId()));
        }

        return $this->render('transactions/new.html.twig', array(
                    'transaction' => $transaction,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Transactions entity.
     *
     * @Route("/newIncome", name="new_income")
     * @Method({"GET", "POST"})
     */
    public function newIncomeAction(Request $request)
    {
        $transaction = new Transactions();
        $form = $this->createForm('AppBundle\Form\TransactionsType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_show', array('id' => $transaction->getId()));
        }

        return $this->render('transactions/new.html.twig', array(
                    'transaction' => $transaction,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Transactions entity.
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
     * Displays a form to edit an existing Transactions entity.
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_edit', array('id' => $transaction->getId()));
        }

        return $this->render('transactions/edit.html.twig', array(
                    'transaction' => $transaction,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Transactions entity.
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
            $em->flush();
        }

        return $this->redirectToRoute('transactions_index');
    }

    /**
     * Creates a form to delete a Transactions entity.
     *
     * @param Transactions $transaction The Transactions entity
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

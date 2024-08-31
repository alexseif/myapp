<?php

namespace App\Controller;

use App\Entity\Accounts;
use App\Entity\AccountTransactions;
use App\Form\AccountTransactionsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * AccountTransactions controller.
 *
 * @Route("/accounttransactions")
 */
class AccountTransactionsController extends AbstractController
{

    /**
     * Lists all AccountTransactions entities.
     *
     * @Route("/", name="accounttransactions_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $accountTransactions = $em->getRepository(AccountTransactions::class)
          ->findAll();

        return $this->render('accounttransactions/index.html.twig', [
          'accountTransactions' => $accountTransactions,
        ]);
    }

    /**
     * Creates a new AccountTransactions entity.
     *
     * @Route("/new", name="accounttransactions_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $em = $entityManager;
        $accountTransaction = new AccountTransactions();
        if (!empty($request->get('account'))) {
            $account = $em->getRepository(Accounts::class)->find(
              $request->get('account')
            );
            $accountTransaction->setAccount($account);
        }
        if (!empty($request->get('amount'))) {
            $accountTransaction->setAmount($request->get('amount'));
        }
        $form = $this->createForm(
          AccountTransactionsType::class,
          $accountTransaction
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($accountTransaction);
            $em->flush();

            return $this->redirectToRoute(
              'accounttransactions_show',
              ['id' => $accountTransaction->getId()]
            );
        }

        return $this->render('accounttransactions/new.html.twig', [
          'accountTransaction' => $accountTransaction,
          'transaction_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a AccountTransactions entity.
     *
     * @Route("/{id}", name="accounttransactions_show", methods={"GET"})
     */
    public function showAction(AccountTransactions $accountTransaction): \Symfony\Component\HttpFoundation\Response
    {
        $deleteForm = $this->createDeleteForm($accountTransaction);

        return $this->render('accounttransactions/show.html.twig', [
          'accountTransaction' => $accountTransaction,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing AccountTransactions entity.
     *
     * @Route("/{id}/edit", name="accounttransactions_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      AccountTransactions $accountTransaction,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($accountTransaction);
        $editForm = $this->createForm(
          AccountTransactionsType::class,
          $accountTransaction
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $entityManager;
            $em->persist($accountTransaction);
            $em->flush();

            return $this->redirectToRoute(
              'accounttransactions_edit',
              ['id' => $accountTransaction->getId()]
            );
        }

        return $this->render('accounttransactions/edit.html.twig', [
          'accountTransaction' => $accountTransaction,
          'transaction_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a AccountTransactions entity.
     *
     * @Route("/{id}", name="accounttransactions_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      AccountTransactions $accountTransaction,
      EntityManagerInterface $entityManager
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $form = $this->createDeleteForm($accountTransaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
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
     * @return FormInterface The form
     */
    private function createDeleteForm(AccountTransactions $accountTransaction): FormInterface
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl(
              'accounttransactions_delete',
              ['id' => $accountTransaction->getId()]
            )
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Accounts;
use AppBundle\Form\AccountsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Accounts controller.
 *
 * @Route("/accounts")
 */
class AccountsController extends Controller
{

    /**
     * Lists all Accounts entities.
     *
     * @Route("/", name="accounts_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accounts = $em->getRepository(Accounts::class)->findWithBalance();
        return $this->render('accounts/index.html.twig', [
          'accounts' => $accounts,
        ]);
    }

    /**
     * Creates a new Accounts entity.
     *
     * @Route("/new", name="accounts_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $account = new Accounts();
        $form = $this->createForm(AccountsType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute(
              'accounts_show',
              ['id' => $account->getId()]
            );
        }

        return $this->render('accounts/new.html.twig', [
          'account' => $account,
          'account_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Accounts entity.
     *
     * @Route("/{id}", name="accounts_show", methods={"GET"})
     */
    public function showAction(Accounts $account)
    {
        $deleteForm = $this->createDeleteForm($account);

        return $this->render('accounts/show.html.twig', [
          'account' => $account,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Accounts entity.
     *
     * @Route("/{id}/edit", name="accounts_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Accounts $account)
    {
        $deleteForm = $this->createDeleteForm($account);
        $editForm = $this->createForm(AccountsType::class, $account);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute(
              'accounts_edit',
              ['id' => $account->getId()]
            );
        }

        return $this->render('accounts/edit.html.twig', [
          'account' => $account,
          'account_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Accounts entity.
     *
     * @Route("/{id}", name="accounts_delete", methods={"DELETE"})
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
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Accounts $account)
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('accounts_delete', ['id' => $account->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}

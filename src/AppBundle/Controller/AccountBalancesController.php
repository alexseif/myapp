<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\AccountBalances;
use AppBundle\Form\AccountBalancesType;

/**
 * AccountBalances controller.
 *
 * @Route("/accountbalances")
 */
class AccountBalancesController extends Controller
{

    /**
     * Lists all AccountBalances entities.
     *
     * @Route("/", name="accountbalances_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accountBalances = $em->getRepository('AppBundle:AccountBalances')->findAll();

        return $this->render('accountbalances/index.html.twig', array(
                    'accountBalances' => $accountBalances,
        ));
    }

    /**
     * Creates a new AccountBalances entity.
     *
     * @Route("/new", name="accountbalances_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $accountBalance = new AccountBalances();
        $form = $this->createForm('AppBundle\Form\AccountBalancesType', $accountBalance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountBalance);
            $em->flush();

            return $this->redirectToRoute('accountbalances_show', array('id' => $accountbalance->getId()));
        }

        return $this->render('accountbalances/new.html.twig', array(
                    'accountBalance' => $accountBalance,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountBalances entity.
     *
     * @Route("/{id}", name="accountbalances_show")
     * @Method("GET")
     */
    public function showAction(AccountBalances $accountBalance)
    {
        $deleteForm = $this->createDeleteForm($accountBalance);

        return $this->render('accountbalances/show.html.twig', array(
                    'accountBalance' => $accountBalance,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccountBalances entity.
     *
     * @Route("/{id}/edit", name="accountbalances_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AccountBalances $accountBalance)
    {
        $deleteForm = $this->createDeleteForm($accountBalance);
        $editForm = $this->createForm('AppBundle\Form\AccountBalancesType', $accountBalance);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountBalance);
            $em->flush();

            return $this->redirectToRoute('accountbalances_edit', array('id' => $accountBalance->getId()));
        }

        return $this->render('accountbalances/edit.html.twig', array(
                    'accountBalance' => $accountBalance,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a AccountBalances entity.
     *
     * @Route("/{id}", name="accountbalances_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AccountBalances $accountBalance)
    {
        $form = $this->createDeleteForm($accountBalance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($accountBalance);
            $em->flush();
        }

        return $this->redirectToRoute('accountbalances_index');
    }

    /**
     * Creates a form to delete a AccountBalances entity.
     *
     * @param AccountBalances $accountBalance The AccountBalances entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AccountBalances $accountBalance)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('accountbalances_delete', array('id' => $accountBalance->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\AccountPayments;
use AppBundle\Form\AccountPaymentsType;

/**
 * AccountPayments controller.
 *
 * @Route("/accountpayments")
 */
class AccountPaymentsController extends Controller
{

    /**
     * Lists all AccountPayments entities.
     *
     * @Route("/", name="accountpayments_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accountPayments = $em->getRepository('AppBundle:AccountPayments')->findAll();

        return $this->render('accountpayments/index.html.twig', array(
                    'accountPayments' => $accountPayments,
        ));
    }

    /**
     * Creates a new AccountPayments entity.
     *
     * @Route("/new", name="accountpayments_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $accountPayment = new AccountPayments();
        $form = $this->createForm('AppBundle\Form\AccountPaymentsType', $accountPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountPayment);
            $em->flush();

            return $this->redirectToRoute('accountpayments_show', array('id' => $accountPayment->getId()));
        }

        return $this->render('accountpayments/new.html.twig', array(
                    'accountPayment' => $accountPayment,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountPayments entity.
     *
     * @Route("/{id}", name="accountpayments_show")
     * @Method("GET")
     */
    public function showAction(AccountPayments $accountPayment)
    {
        $deleteForm = $this->createDeleteForm($accountPayment);

        return $this->render('accountpayments/show.html.twig', array(
                    'accountPayment' => $accountPayment,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccountPayments entity.
     *
     * @Route("/{id}/edit", name="accountpayments_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AccountPayments $accountPayment)
    {
        $deleteForm = $this->createDeleteForm($accountPayment);
        $editForm = $this->createForm('AppBundle\Form\AccountPaymentsType', $accountPayment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($accountPayment);
            $em->flush();

            return $this->redirectToRoute('accountpayments_edit', array('id' => $accountPayment->getId()));
        }

        return $this->render('accountpayments/edit.html.twig', array(
                    'accountPayment' => $accountPayment,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a AccountPayments entity.
     *
     * @Route("/{id}", name="accountpayments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AccountPayments $accountPayment)
    {
        $form = $this->createDeleteForm($accountPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($accountPayment);
            $em->flush();
        }

        return $this->redirectToRoute('accountpayments_index');
    }

    /**
     * Creates a form to delete a AccountPayments entity.
     *
     * @param AccountPayments $accountPayment The AccountPayments entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AccountPayments $accountPayment)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('accountpayments_delete', array('id' => $accountPayment->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

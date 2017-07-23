<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ScratchPad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Scratchpad controller.
 *
 * @Route("scratchpad")
 */
class ScratchPadController extends Controller
{

  /**
   * Lists all scratchPad entities.
   *
   * @Route("/", name="scratchpad_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $scratchPads = $em->getRepository('AppBundle:ScratchPad')->findAll();

    return $this->render('scratchpad/index.html.twig', array(
          'scratchPads' => $scratchPads,
    ));
  }

  /**
   * Creates a new scratchPad entity.
   *
   * @Route("/new", name="scratchpad_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $scratchPad = new Scratchpad();
    $form = $this->createForm('AppBundle\Form\ScratchPadType', $scratchPad);
    $form->handleRequest($request);

    if ($request->isXmlHttpRequest() & "POST" == $request->getMethod()) {
      $scratchPad->setContent($request->get('content'));
      $em = $this->getDoctrine()->getManager();
      $em->persist($scratchPad);
      $em->flush($scratchPad);

      $responseData = array("redirect" => $this->generateUrl('scratchpad_edit', array('id' => $scratchPad->getId())));
      $response = new Response(json_encode($responseData));
      return $response;
    }

    return $this->render('scratchpad/new.html.twig', array(
          'scratchPad' => $scratchPad,
          'form' => $form->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing scratchPad entity.
   *
   * @Route("/{id}/edit", name="scratchpad_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, ScratchPad $scratchPad)
  {
    $deleteForm = $this->createDeleteForm($scratchPad);
    $editForm = $this->createForm('AppBundle\Form\ScratchPadType', $scratchPad);
    $editForm->handleRequest($request);


    if ($request->isXmlHttpRequest() & "POST" == $request->getMethod()) {
      dump($request);
      $scratchPad->setContent($request->get('content'));
      $this->getDoctrine()->getManager()->flush();

      return new Response();
    }

    return $this->render('scratchpad/edit.html.twig', array(
          'scratchPad' => $scratchPad,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a scratchPad entity.
   *
   * @Route("/{id}", name="scratchpad_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, ScratchPad $scratchPad)
  {
    $form = $this->createDeleteForm($scratchPad);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($scratchPad);
      $em->flush($scratchPad);
    }

    return $this->redirectToRoute('scratchpad_index');
  }

  /**
   * Creates a form to delete a scratchPad entity.
   *
   * @param ScratchPad $scratchPad The scratchPad entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(ScratchPad $scratchPad)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('scratchpad_delete', array('id' => $scratchPad->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

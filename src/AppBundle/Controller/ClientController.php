<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Client controller.
 *
 * @Route("client")
 */
class ClientController extends Controller
{

  /**
   * Lists all client entities.
   *
   * @Route("/", name="client_index", methods={"GET"})
   * @Template("AppBundle:client:index.html.twig")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $clients = $em->getRepository('AppBundle:Client')->findAll();

    return array(
      'clients' => $clients,
    );
  }

  /**
   * Creates a new client entity.
   *
   * @Route("/new", name="client_new", methods={"GET", "POST"})
   * @Template("AppBundle:client:new.html.twig")
   */
  public function newAction(Request $request)
  {
    $client = new Client();
    $form = $this->createForm('AppBundle\Form\ClientType', $client);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($client);
      $em->flush($client);

      return $this->redirectToRoute('client_show', array('id' => $client->getId()));
    }

    return array(
      'client' => $client,
      'form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a client entity.
   *
   * @Route("/{id}", name="client_show", methods={"GET"})
   * @Template("AppBundle:client:show.html.twig")
   */
  public function showAction(Client $client)
  {
    $deleteForm = $this->createDeleteForm($client);

    return array(
      'client' => $client,
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing client entity.
   *
   * @Route("/{id}/edit", name="client_edit", methods={"GET", "POST"})
   * @Template("AppBundle:client:edit.html.twig")
   */
  public function editAction(Request $request, Client $client)
  {
    $deleteForm = $this->createDeleteForm($client);
    $editForm = $this->createForm('AppBundle\Form\ClientType', $client);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('client_edit', array('id' => $client->getId()));
    }

    return array(
      'client' => $client,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Deletes a client entity.
   *
   * @Route("/{id}", name="client_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, Client $client)
  {
    $form = $this->createDeleteForm($client);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($client);
      $em->flush($client);
    }

    return $this->redirectToRoute('client_index');
  }

  /**
   * Creates a form to delete a client entity.
   *
   * @param Client $client The client entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Client $client)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $client->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}

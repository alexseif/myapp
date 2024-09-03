<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Client controller.
 *
 * @Route("client")
 */
class ClientController extends AbstractController
{

    /**
     * Lists all client entities.
     *
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;

        $clients = $em->getRepository(Client::class)->findAll();

        return $this->render('client/index.html.twig', [
          'clients' => $clients,
        ]);
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="client_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($client);
            $em->flush($client);
            $this->addFlash('success', 'Client saved');

            return $this->redirectToRoute(
              'client_show',
              ['id' => $client->getId()]
            );
        }

        return $this->render('client/new.html.twig', [
          'client' => $client,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a client entity.
     *
     * @Route("/{id}", name="client_show", methods={"GET"})
     */
    public function showAction(Client $client
    ): Response {
        $deleteForm = $this->createDeleteForm($client);

        return $this->render('client/show.html.twig', [
          'client' => $client,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="client_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      Client $client,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($client);
        $editForm = $this->createForm(ClientType::class, $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Client updated');

            return $this->redirectToRoute(
              'client_show',
              ['id' => $client->getId()]
            );
        }

        return $this->render('client/edit.html.twig', [
          'client' => $client,
          'form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}", name="client_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      Client $client,
      EntityManagerInterface $entityManager
    ): RedirectResponse {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($client);
            $em->flush($client);
            $this->addFlash('success', 'Client deleted');
        }

        return $this->redirectToRoute('client_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Client $client The client entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Client $client): FormInterface
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('client_delete', ['id' => $client->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}

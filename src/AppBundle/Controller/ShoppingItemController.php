<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShoppingItem;
use AppBundle\Form\ShoppingItemType;
use AppBundle\Repository\ShoppingItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shopping/item")
 */
class ShoppingItemController extends Controller
{
    /**
     * @Route("/", name="shopping_item_index", methods={"GET"})
     */
    public function index(ShoppingItemRepository $shoppingItemRepository): Response
    {
        return $this->render('shopping_item/index.html.twig', [
            'shopping_items' => $shoppingItemRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="shopping_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $shoppingItem = new ShoppingItem();
        $form = $this->createForm(ShoppingItemType::class, $shoppingItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shoppingItem);
            $entityManager->flush();

            return $this->redirectToRoute('shopping_item_index');
        }

        return $this->render('shopping_item/new.html.twig', [
            'shopping_item' => $shoppingItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="shopping_item_show", methods={"GET"})
     */
    public function show(ShoppingItem $shoppingItem): Response
    {
        return $this->render('shopping_item/show.html.twig', [
            'shopping_item' => $shoppingItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="shopping_item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ShoppingItem $shoppingItem): Response
    {
        $form = $this->createForm(ShoppingItemType::class, $shoppingItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shopping_item_index');
        }

        return $this->render('shopping_item/edit.html.twig', [
            'shopping_item' => $shoppingItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="shopping_item_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ShoppingItem $shoppingItem): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shoppingItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($shoppingItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('shopping_item_index');
    }
}

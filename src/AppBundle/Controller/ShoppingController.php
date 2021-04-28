<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ShoppingListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shopping", name="shopping_")
 */
class ShoppingController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {

        return $this->render('shopping/index.html.twig', [
            'shopping_lists' => $shoppingListRepository->findAll(),
            'controller_name' => 'ShoppingController',
        ]);
    }
}

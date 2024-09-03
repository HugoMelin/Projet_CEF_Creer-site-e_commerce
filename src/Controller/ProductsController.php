<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductFilterType;
use App\Repository\SweatShirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductsController extends AbstractController
{
    #[Route('/products', name: 'products.index')]
    public function index(Request $request, SweatShirtRepository $repository): Response
    {
        $form = $this->createForm(ProductFilterType::class);
        $form->handleRequest($request);
        $prices = array_map('intval', explode(",", trim($form->get('priceRange')->getData(), "[]")));

        $products = $repository->findWithPriceRange($prices);

        return $this->render('products/index.html.twig', [
            'form' => $form->createView(),
            'products' => $products
        ]);
    }

    #[Route('/product/{id}', name:'products.showById')]
    public function showById(string $id, SweatShirtRepository $repository): Response
    {
        $sweat = $repository->findOneById($id);
        return $this->render('products/showById.html.twig', [
            'sweat'=> $sweat,
        ]);
    }
}

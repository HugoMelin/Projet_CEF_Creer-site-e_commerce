<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductFilterType;
use App\Repository\SweatShirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ProductsController handles product-related operations.
 * 
 * This controller is responsible for displaying product listings
 * and individual product details.
 */
class ProductsController extends AbstractController
{
    /**
     * Displays a list of products with optional filtering.
     *
     * This method renders a page with a list of products. It also
     * processes a filter form to allow users to filter products by price range.
     *
     * @param Request $request The current request
     * @param SweatShirtRepository $repository The repository for SweatShirt entities
     * @return Response A response instance with the rendered product list view
     */
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

    /**
     * Displays details of a specific product.
     *
     * This method fetches and displays the details of a single product
     * based on its ID.
     *
     * @param string $id The ID of the product to display
     * @param SweatShirtRepository $repository The repository for SweatShirt entities
     * @return Response A response instance with the rendered product detail view
     */
    #[Route('/product/{id}', name:'products.showById')]
    public function showById(string $id, SweatShirtRepository $repository): Response
    {
        $sweat = $repository->findOneById($id);
        return $this->render('products/showById.html.twig', [
            'sweat'=> $sweat,
        ]);
    }
}

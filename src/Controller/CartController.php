<?php
namespace App\Controller;

use App\Entity\SweatShirt;
use App\Repository\SweatShirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * CartController handles all cart-related operations.
 * 
 * This controller is responsible for managing the shopping cart,
 * including adding/removing items, checkout process, and payment processing.
*/
#[Route('/cart', name:'cart.')]
class CartController extends AbstractController
{
    private $stripe;
    private $repository;

    /**
     * CartController constructor.
     *
     * @param StripeClient $stripe The Stripe client for payment processing
     * @param SweatShirtRepository $repository The repository for SweatShirt entities
     */
    public function __construct(StripeClient $stripe, SweatShirtRepository $repository)
    {
        $this->stripe = $stripe;
        $this->repository = $repository;
    }

    /**
     * Displays the cart contents.
     *
     * @param SessionInterface $session The session interface
     * @param SweatShirtRepository $repository The SweatShirt repository
     * @return Response A response instance with the rendered cart view
     */
    #[Route(path:'/', name:'index')]
    public function index(SessionInterface $session, SweatShirtRepository $repository): Response
    {
        $cart = $session->get('cart', []);
        $data = $this->prepareCartData($cart);
        $total = $this->calculateTotal($cart);

        return $this->render('cart/index.html.twig', [
            'items' => $data,
            'total' => $total,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY_TEST']
        ]);
    }

    /**
     * Adds a sweatshirt to the cart.
     *
     * @param SweatShirt $sweatShirt The sweatshirt to add
     * @param SessionInterface $session The session interface
     * @param string $size The size of the sweatshirt
     * @return Response A redirect response to the cart index
     */
    #[Route(path:'/add/{id}-{size}', name:'add')]
    public function add(SweatShirt $sweatShirt, SessionInterface $session, $size): Response
    {
        $id = $sweatShirt->getId();
        $cart = $session->get('cart', []);

        if (!isset($cart[$id])) {
            $cart[$id] = ['quantity' => 1, 'option' => [$size => 1]];
        } else {
            $cart[$id]['quantity']++;
            $cart[$id]['option'][$size] = ($cart[$id]['option'][$size] ?? 0) + 1;
        }

        $session->set('cart', $cart);
        
        return $this->redirectToRoute('cart.index');
    }

    /**
     * Removes a sweatshirt from the cart.
     *
     * @param int $id The ID of the sweatshirt to remove
     * @param string $size The size of the sweatshirt to remove
     * @param SessionInterface $session The session interface
     * @return Response A redirect response to the cart index
     */
    #[Route(path:'/remove/{id}-{size}', name:'remove')]
    public function remove($id, $size, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            if (isset($cart[$id]['option'][$size]) && $cart[$id]['option'][$size] > 0) {
                $cart[$id]['option'][$size]--;
                $cart[$id]['quantity']--;

                if ($cart[$id]['option'][$size] == 0) {
                    unset($cart[$id]['option'][$size]);
                }

                if ($cart[$id]['quantity'] == 0) {
                    unset($cart[$id]);
                }
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart.index');
    }

    /**
     * Clears the entire cart.
     *
     * @param SessionInterface $session The session interface
     * @return Response A redirect response to the cart index
     */
    #[Route(path:'/clear', name:'clear')]
    public function clear(SessionInterface $session): Response
    {
        // Supprime complètement le panier de la session
        $session->remove('cart');

        // Ajoute un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Votre panier a été vidé avec succès.');

        // Redirige vers la page du panier
        return $this->redirectToRoute('cart.index');
    }

    /**
     * Handles the checkout process.
     *
     * @param SessionInterface $session The session interface
     * @return Response A response instance with the checkout confirmation
     */
    #[Route(path:'/checkout', name:'checkout')]
    public function checkout(SessionInterface $session): Response
    {
        // Une fois la commande traitée, videz le panier
        $session->remove('cart');

        return $this->render('cart/checkout.html.twig', [
            'message' => 'Votre commande a été traitée avec succès!'
        ]);
    }

    /**
     * Processes the payment using Stripe.
     *
     * @param SessionInterface $session The session interface
     * @return JsonResponse A JSON response with the Stripe session ID or error message
     */
    #[Route('/process-payment', name: 'process_payment', methods: ['POST'])]
    public function processPayment(SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);
        $total = $this->calculateTotal($cart);

        try {
            $checkoutSession = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $total * 100,
                        'product_data' => [
                            'name' => 'Achat sur votre site',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('cart.success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cart.cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return new JsonResponse(['id' => $checkoutSession->id]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handles successful payment.
     *
     * @param SessionInterface $session The session interface
     * @return Response A response instance with the success message
     */
    #[Route('/success', name: 'success')]
    public function success(SessionInterface $session): Response
    {
        $session->remove('cart');
        return $this->render('cart/success.html.twig');
    }

    /**
     * Handles cancelled payment.
     *
     * @return Response A response instance with the cancellation message
     */
    #[Route('/cancel', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('cart/cancel.html.twig');
    }

    /**
     * Calculates the total price of items in the cart.
     *
     * @param array $cart The cart array from the session
     * @return float The total price of all items in the cart
     */
    private function calculateTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $id => $item) {
            $product = $this->repository->find($id);
            if ($product) {
                $total += $product->getPrice() * $item['quantity'];
            }
        }
        return $total;
    }

    /**
     * Prepares the cart data for display.
     *
     * @param array $cart The cart array from the session
     * @return array An array of prepared cart data
     */
    private function prepareCartData(array $cart): array
    {
        $data = [];
        foreach ($cart as $id => $item) {
            $product = $this->repository->find($id);
            if ($product) {
                $data[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'option' => $item['option'],
                    'subtotal' => $product->getPrice() * $item['quantity']
                ];
            }
        }
        return $data;
    }
}
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

#[Route('/cart', name:'cart.')]
class CartController extends AbstractController
{
    private $stripe;
    private $repository;

    public function __construct(StripeClient $stripe, SweatShirtRepository $repository)
    {
        $this->stripe = $stripe;
        $this->repository = $repository;
    }

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

    #[Route(path:'/checkout', name:'checkout')]
    public function checkout(SessionInterface $session): Response
    {
        // Ici, vous pouvez ajouter la logique pour valider et régler la commande
        // Par exemple, créer une entité Order, la sauvegarder en base de données, etc.

        // Une fois la commande traitée, videz le panier
        $session->remove('cart');

        return $this->render('cart/checkout.html.twig', [
            'message' => 'Votre commande a été traitée avec succès!'
        ]);
    }

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

    #[Route('/success', name: 'success')]
    public function success(SessionInterface $session): Response
    {
        $session->remove('cart');
        return $this->render('cart/success.html.twig');
    }

    #[Route('/cancel', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('cart/cancel.html.twig');
    }

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
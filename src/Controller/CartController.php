<?php
namespace App\Controller;

use App\Entity\SweatShirt;
use App\Repository\SweatShirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[Route('/cart', name:'cart.')]
class CartController extends AbstractController
{
    #[Route(path:'/', name:'index')]
    public function index(SessionInterface $session, SweatShirtRepository $repository): Response
    {
        $cart = $session->get('cart', []);
        $data = [];
        $total = 0;

        foreach ($cart as $id => $value) {
            $product = $repository->find($id);
            if ($product) {
                $data[] = [
                    'product' => $product,
                    'quantity' => $value['quantity'],
                    'option' => $value['option'],
                    'subtotal' => $product->getPrice() * $value['quantity']
                ];
                $total += $data[count($data) - 1]['subtotal'];
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $data,
            'total' => $total
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
}
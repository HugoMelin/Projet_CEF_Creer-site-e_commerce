<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SweatShirtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HomeController handles the main page of the application.
 * 
 * This controller is responsible for rendering the home page
 * and displaying featured sweatshirts.
 */
class HomeController extends AbstractController
{
    /**
     * Renders the home page with featured sweatshirts.
     *
     * This method fetches the top sweatshirts from the repository
     * and passes them to the view for display on the home page.
     *
     * @param SweatShirtRepository $repository The repository for SweatShirt entities
     * @return Response A response instance with the rendered home page
     */
    #[Route('/', name: 'home.index')]
    public function index(SweatShirtRepository $repository): Response
    {
        $sweats = $repository->showTop();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'Accueil',
            'sweats'=> $sweats,
        ]);
    }
}

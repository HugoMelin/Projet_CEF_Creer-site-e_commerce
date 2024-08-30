<?php

namespace App\Controller;

use App\Repository\SweatShirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
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

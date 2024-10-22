<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController handles user authentication processes.
 * 
 * This controller manages login and logout functionality for the application.
 */
class SecurityController extends AbstractController
{
    /**
     * Handles the login process.
     *
     * This method renders the login form and processes login attempts.
     * It captures authentication errors and the last entered username.
     *
     * @param AuthenticationUtils $authenticationUtils Utility for handling authentication
     * @return Response A response instance with the rendered login form
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Handles the logout process.
     *
     * This method is intercepted by the logout key on the firewall.
     * It doesn't need to have any logic as the logout is handled by Symfony's security system.
     *
     * @throws \LogicException This exception is thrown to indicate that this method should not be called directly
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

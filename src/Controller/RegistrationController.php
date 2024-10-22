<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * RegistrationController handles user registration and email verification.
 */
class RegistrationController extends AbstractController
{
    /**
     * Constructor for RegistrationController.
     *
     * @param EmailVerifier $emailVerifier Service for handling email verification
     */
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    /**
     * Handles user registration process.
     *
     * This method creates a new user, processes the registration form,
     * and sends a confirmation email.
     *
     * @param Request $request Current request
     * @param UserPasswordHasherInterface $userPasswordHasher Password hashing service
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     * @return Response Rendered registration form or redirect response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@stubborn.com', 'Stubborn Mailer'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('home.index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Verifies user email and authenticates the user.
     *
     * This method handles the email verification process, marks the user as verified,
     * and logs them in if successful.
     *
     * @param Request $request Current request
     * @param TranslatorInterface $translator Translation service
     * @param UserRepository $userRepository Repository for User entities
     * @param UserAuthenticatorInterface $userAuthenticator User authentication service
     * @param AuthenticatorInterface $authenticator Authenticator for user login
     * @return Response Redirect response based on verification result
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository, UserAuthenticatorInterface $userAuthenticator, AuthenticatorInterface $authenticator): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        //Connecter l'utilisateur
        $response = $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        if ($response instanceof Response) {
            return $response;
        }
        return $this->redirectToRoute('home.index');
    }
}

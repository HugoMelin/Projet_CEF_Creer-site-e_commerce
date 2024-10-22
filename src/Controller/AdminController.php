<?php

namespace App\Controller;

use App\Entity\SweatShirt;
use App\Entity\Image;
use App\Form\SweatShirtType;
use App\Repository\SweatShirtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * AdminController handles all admin-related operations for sweatshirts.
 * 
 * This controller is responsible for managing the CRUD operations
 * for sweatshirts in the admin panel.
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Displays the admin dashboard with a list of all sweatshirts.
     *
     * @param SweatShirtRepository $repository The repository to fetch sweatshirts
     * @return Response A response instance with the rendered view
     */
    #[Route('/', name: 'admin_dashboard')]
    public function index(SweatShirtRepository $repository): Response
    {
        $sweatshirts = $repository->findAll();
        return $this->render('admin/index.html.twig', [
            'sweatshirts' => $sweatshirts,
        ]);
    }

    /**
     * Handles the creation of a new sweatshirt.
     *
     * This method processes the form submission for creating a new sweatshirt,
     * including handling file uploads for the sweatshirt image.
     *
     * @param Request $request The current request
     * @param EntityManagerInterface $entityManager The entity manager
     * @param SluggerInterface $slugger The slugger service for filename sanitization
     * @return Response A response instance with either the form view or a redirect
     */
    #[Route('/sweatshirt/new', name: 'admin_sweatshirt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $sweatshirt = new SweatShirt();
        $form = $this->createForm(SweatShirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('sweatshirt_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... gérer l'exception si quelque chose se passe pendant l'upload
                }

                $image = new Image();
                $image->setName($newFilename);
                $image->setLink('/img/products/'.$newFilename);
                $image->setAlt($sweatshirt->getName());
                $image->setIdsweatshirt($sweatshirt);

                $entityManager->persist($image);
            }

            $entityManager->persist($sweatshirt);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/sweatshirt_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * Handles the editing of an existing sweatshirt.
     *
     * This method processes the form submission for editing a sweatshirt,
     * including handling file uploads for updating the sweatshirt image.
     *
     * @param Request $request The current request
     * @param SweatShirt $sweatshirt The sweatshirt entity to edit
     * @param EntityManagerInterface $entityManager The entity manager
     * @param SluggerInterface $slugger The slugger service for filename sanitization
     * @return Response A response instance with either the form view or a redirect
     */
    #[Route('/sweatshirt/{id}/edit', name: 'admin_sweatshirt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SweatShirt $sweatshirt, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SweatShirtType::class, $sweatshirt);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('sweatshirt_images_directory'),
                        $newFilename
                    );
    
                    // Supprimer l'ancienne image si elle existe
                    $oldImage = $sweatshirt->getImages()->first();
                    if ($oldImage) {
                        $oldImagePath = $this->getParameter('sweatshirt_images_directory').'/'.$oldImage->getName();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        $entityManager->remove($oldImage);
                    }
    
                    // Créer une nouvelle entité Image
                    $image = new Image();
                    $image->setName($newFilename);
                    $image->setLink('/img/products/'.$newFilename);
                    $image->setAlt($sweatshirt->getName());
                    $image->setIdsweatshirt($sweatshirt);
    
                    $entityManager->persist($image);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }
    
            $entityManager->flush();
    
            $this->addFlash('success', 'Le sweat-shirt a été mis à jour avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }
    
        return $this->render('admin/sweatshirt_form.html.twig', [
            'form' => $form->createView(),
            'sweatshirt' => $sweatshirt,
        ]);
    }

    /**
     * Handles the deletion of a sweatshirt.
     *
     * This method processes the deletion of a sweatshirt and its associated images,
     * both from the database and the file system.
     *
     * @param Request $request The current request
     * @param SweatShirt $sweatshirt The sweatshirt entity to delete
     * @param EntityManagerInterface $entityManager The entity manager
     * @return Response A redirect response to the admin dashboard
     */
    #[Route('/sweatshirt/{id}/delete', name: 'admin_sweatshirt_delete', methods: ['POST'])]
    public function delete(Request $request, SweatShirt $sweatshirt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sweatshirt->getId(), $request->request->get('_token'))) {
            // Récupérer toutes les images associées au sweat-shirt
            $images = $sweatshirt->getImages();

            // Supprimer chaque image
            foreach ($images as $image) {
                // Supprimer le fichier physique
                $imagePath = $this->getParameter('sweatshirt_images_directory').'/'.$image->getName();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                // Supprimer l'entité Image
                $entityManager->remove($image);
            }

            // Supprimer le sweat-shirt
            $entityManager->remove($sweatshirt);
            $entityManager->flush();

            $this->addFlash('success', 'Le sweat-shirt et ses images ont été supprimés avec succès.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            // Redirection avec flash message
            $this->addFlash('success', 'Votre profil à été mis à jour');
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('user/index.html.twig', [
            'userForm' => $form,
        ]);
    }

    #[Route('/complete', name: 'app_complete', methods: ['POST'])]
    public function complete(Request $request, EntityManagerInterface $em): Response
    {
        $data = $request->getPayload(); // on récupère les données du formulaire
        if (!empty($data->get('username')) && !empty($data->get('fullname'))) {
            // Enregistrer les données dans la base de données
            $user = $this->getUser(); // Ici on récupère l'utilisateur actuel
            $user
                ->setUsername($data->get('username')) // on met à jour username
                ->setFullname($data->get('fullname')) // on met à jour fullname
                ;
            $em->persist($user); // on persiste l'utilisateur
            $em->flush(); // on sauvegarde les modifications en base de données
            
            // Redirection avec flash message
            $this->addFlash('success', 'Votre profil est complété');
        } else {
            $this->addFlash('error', 'Vous devez remplir tous les champs');
        }
        return $this->redirectToRoute('app_profile');
    }
}
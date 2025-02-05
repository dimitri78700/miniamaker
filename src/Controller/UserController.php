<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/complete', name: 'app_complete', methods: ['POST'])]
    public function complete(Request $request, EntityManagerInterface $em): Response
    {
        $username = $request->getPayload()->get('username');
        $fullname = $request->getPayload()->get('fullname');

        if (!empty($username) && !empty($fullname)) {
            $user = $this->getUser();
            $user->setUsername($username)
                 ->setFullname($fullname);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre email a bien été vérifié, Merci.');
        } else {
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
        }
        return $this->redirectToRoute('app_profile');
    }
}

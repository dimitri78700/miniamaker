<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function complete(Request $request): Response
    {
        $username = $request->request->get('username');
        $fullname = $request->request->get('fullname');

        if (!empty($username) && !empty($fullname)) {
            
        }
        $this->addFlash('success', 'Votre email a bien été vérifié, Merci.');
        return $this->redirectToRoute('app_profile');
    }
}

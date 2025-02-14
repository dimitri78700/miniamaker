<?php

namespace App\Controller;

use App\Service\PaymentService;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class SubscriptionController extends AbstractController
{

    private Subscription $subscription;
    public function __construct(
        private EntityManagerInterface $em
    ) {
        $this->subscription = $this->getUser()->getSubscription();
    }


    #[Route('/subscription', name: 'app_subscription', methods: ['POST'])]
    public function subscription(Request $request, PaymentService $ps): RedirectResponse
    {
        try {

            if ($this->subscription == null || $this->subscription->isActive() === false) {
                $checkoutUrl = $ps->setPayment(
                    $this->getUser(),
                    intval($request->get('plan'))
                );
                return $this->redirectToRoute('app_subscription_check', ['link' => $checkoutUrl]);
                // return new RedirectResponse($checkoutUrl);
            }

            $this->addFlash('warning', "Vous êtes déjà abonné(e)");
            return $this->redirectToRoute('app_profile');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création du paiement');
            return $this->redirectToRoute('app_profile');
        }
    }

    #[Route('/subscription/check', name: 'app_subscription_check', methods: ['GET'])]
    public function check(Request $request): Response
    {
        // Logique de traitement du succès
        return $this->render('subscription/check.html.twig', [
            'link' => $request->get('link'),
        ]);
    }

    #[Route('/subscription/success', name: 'app_subscription_success', methods: ['GET'])]
    public function success(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Vérification si l'utilisateur possède un abonnement
        $this->subscription = $user->getSubscription();
    
        if ($this->subscription) {
            // Active l'abonnement
            $this->subscription->setIsActive(true);
            
            // Persiste les changements en base de données
            $em->persist($this->subscription);
            $em->flush();
    
            // Message flash pour informer l'utilisateur
            $this->addFlash('success', 'Votre abonnement a été validé et votre compte est désormais activé.');
        } else {
            // Dans le cas où il n'y aurait pas d'abonnement, on peut afficher un message d'erreur ou de warning
            $this->addFlash('warning', 'Aucun abonnement n\'a été trouvé pour activer votre compte.');
        }
    
        // Redirige vers le profil
        return $this->redirectToRoute('app_subscription_return', ['status' => 'success']);
    }

    #[Route('/subscription/cancel', name: 'app_subscription_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        // Logique de traitement de l'annulation
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/subscription/return', name: 'app_subscription_return', methods: ['GET'])]
    public function subscriptionReturn(Request $request, EntityManagerInterface $em): Response
    {
        $status = $request->query->get('status');
        $user = $this->getUser();

        if ($status === 'success') {
            $this->subscription = $user->getSubscription();
            if ($this->subscription) {
                // Active l'abonnement
                $this->subscription->setIsActive(true);
                $em->persist($this->subscription);
                $em->flush();
                $title = 'Abonnement validé !';
                $message = 'Votre abonnement a été validé et votre compte est désormais activé.';
            } else {
                $title = 'Erreur';
                $message = "Aucun abonnement n'a été trouvé pour activer votre compte.";
            }
        } elseif ($status === 'cancel') {
            $title = 'Abonnement annulé';
            $message = 'Votre abonnement a été annulé. Si vous souhaitez vous abonner à nouveau, n’hésitez pas à réessayer.';
        } else {
            $title = 'Statut inconnu';
            $message = "Le statut de retour n'est pas reconnu.";
        }

        return $this->render('subscription/return.html.twig', [
            'title'   => $title,
            'message' => $message,
            'status'  => $status,
        ]);
    }
}


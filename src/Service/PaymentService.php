<?php

namespace App\Service;


use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Subscription;
use Stripe\Checkout\Session;
use App\Service\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Stripe\PaymentMethod;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentService // extends AbstractService
{
    public function __construct(
        private ParameterBagInterface $params,
        private SubscriptionRepository $sr,
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
    ) {}

    public function setPayment(User $user, int $amount): string
    {
        Stripe::setApiKey($this->params->get('STRIPE_SK'));

        $subscription = new Subscription();
        $subscription
            ->setClient($user)
            ->setAmount($amount)
            ->setFrequency($amount > 99 ? 'year' : 'month')
        ;

        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $amount * 100,
                        'recurring' => [
                            'interval' => $subscription->getFrequency(),
                        ],
                        'product_data' => [
                            'name' => 'Abonnement miniamaker',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $this->params->get('APP_URL') . '/subscription/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->params->get('APP_URL') . '/subscription/cancel',
            ]);

            return $checkout_session->url;
        } catch (\Throwable $th) {
            echo $th->getMessage() . PHP_EOL;
            echo json_encode(['error' => $th->getMessage()]);
        }
    }
}
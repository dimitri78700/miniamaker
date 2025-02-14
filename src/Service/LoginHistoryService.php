<?php

namespace App\Service;

use DeviceDetector\DeviceDetector;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\LoginHistory;


class LoginHistoryService
{
    public function __construct(readonly private EntityManagerInterface $em) {}
    public function addHistory(User $user, string $userAgent, string $ip): void
    {
        $deviceDetector = new DeviceDetector($userAgent);
        $deviceDetector->parse();

            $loginHistory = new LoginHistory();
            $loginHistory
                ->setUser($user)
                ->setIpAdress($ip)
                ->setDevice($deviceDetector->getDeviceName())
                ->setOs($deviceDetector->getOs()['name'])
                ->setBrowser($deviceDetector->getClient()['name'])
            ;
            $this->em->persist($loginHistory);
            $this->em->flush();
        
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Detail;
use App\Entity\Tag;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $slugger;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
        )
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création des tags
        $tags = [];
        $tagNames = ['Cuisine', 'E-commerce', 'Business en ligne', 'Tech', 'IA', 'Personal Branding', 'Design'];
        foreach ($tagNames as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Création des utilisateurs
        for ($i = 0; $i < 40; $i++) {
            $nom = $faker->name();
            $prenom = $faker->firstName();
            $fullname = $prenom . ' ' . $nom;
            $sluggy = strtolower($this->slugger->slug($fullname));

            $user = new User();
            $user->setEmail($sluggy . '@' . $faker->freeEmailDomain());
            $user->setRoles(['ROLE_AGENT']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setUsername($this->slugger->slug($prenom));
            $user->setFullname($fullname);
            $user->setIsMajor(false);
            $user->setIsTerms(true);
            $user->setIsGpdr(true);
            $user->setIsVerified(true);

            $manager->persist($user);
        }

        // Création des utilisateurs PRO
        for ($i = 0; $i < 50; $i++) {
            $nom = $faker->name();
            $prenom = $faker->firstName();
            $fullname = $prenom . ' ' . $nom;
            $sluggy = strtolower($this->slugger->slug($fullname));

            $user = new User();
            $user->setEmail($sluggy . '@' . $faker->freeEmailDomain());
            $user->setRoles(['ROLE_AGENT']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setUsername($this->slugger->slug($prenom));
            $user->setFullname($fullname);
            $user->setIsMajor(false);
            $user->setIsTerms(true);
            $user->setIsGpdr(true);
            $user->setIsVerified(true);
            
            // Créer un détail pour certains utilisateurs (pros)
            if ($faker->boolean(70)) {
                $detail = new Detail();
                $detail->setCompanyNumber($faker->siret());
                $detail->setCompanyName($faker->company());
                $detail->setAddress($faker->streetAddress());
                $detail->setCity($faker->city());
                $detail->setPostalCode($faker->postcode());
                $detail->setPortfolioLink($faker->url());
                $detail->setPro($user);
                
                $manager->persist($detail);
            }

            $manager->persist($user);
        }
        
        // Création des utilisateurs AGENT
        for ($i = 0; $i < 25; $i++) {

            $nom = $faker->name();
            $prenom = $faker->firstName();
            $fullname = $prenom . ' ' . $nom;
            $sluggy = strtolower($this->slugger->slug($fullname));

            $user = new User();
            $user->setEmail($sluggy . '@' . $faker->freeEmailDomain());
            $user->setRoles(['ROLE_AGENT']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setUsername($this->slugger->slug($prenom));
            $user->setFullname($fullname);
            $user->setIsMajor(false);
            $user->setIsTerms(true);
            $user->setIsGpdr(true);
            $user->setIsVerified(true);
            
            // Créer un détail pour certains utilisateurs (pros)
            if ($faker->boolean(70)) {
                $detail = new Detail();
                $detail->setCompanyNumber($faker->siret());
                $detail->setCompanyName($faker->company());
                $detail->setAddress($faker->streetAddress());
                $detail->setCity($faker->city());
                $detail->setPostalCode($faker->postcode());
                $detail->setPortfolioLink($faker->url());
                $detail->setPro($user);
                
                $manager->persist($detail);
            }

            $manager->persist($user);
        }

        // Créer un admin
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setUsername('admin');
        $admin->setFullname('Admin User');
        $admin->setIsMajor(true);
        $admin->setIsTerms(true);
        $admin->setIsGpdr(true);
        $admin->setIsVerified(true);
        
        $manager->persist($admin);

        $manager->flush();
    }
}
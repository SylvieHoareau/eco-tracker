<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Document\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un utilisateur de test
        $user = new User();
        $user->setEmail('test@eco-tracker.com');
        $user->setRoles(['ROLE_USER']);
        // Attention : si tu as un encodeur de mot de passe, mets le hash ici
        $user->setPassword('password123'); 

        $manager->persist($user);

        // Envoyer les données vers MongoDB (Local ou Atlas)
        $manager->flush();
    }
}
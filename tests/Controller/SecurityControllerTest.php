<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginDisplay(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        // Vérifie que la page répond bien (Code 200)
        $this->assertResponseIsSuccessful();
        // Vérifie la présence du titre H1 que nous avons mis
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // On sélectionne le bouton par son texte (ou son ID)
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'hacker@mauvais.com',
            'password' => '123456',
        ]);

        $client->submit($form);

        // On attend une redirection (vers le login avec erreur)
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        
        // Vérifie qu'on affiche bien un message d'alerte
        $this->assertSelectorExists('.alert-danger');
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();

        // Accéder au DocumentManager de MongoDB
        $container = $client->getContainer();
        $dm = $container->get('doctrine_mongodb.odm.document_manager');

        // Créer l'utilisateur de test s'il n'existe pas
        $userRepo = $dm->getRepository(\App\Document\User::class);
        $user = $userRepo->findOneBy(['email' => 'test@eco-tracker.re']);


        if (!$user) {
            $user = new \App\Document\User();
            $user->setEmail('test@eco-tracker.re');
            $user->setUsername('Tester');

            $dm->persist($user);
        }

        // On récupère le service de hachage de Symfony pour avoir un vrai mot de passe valide
        $hasher = $container->get('security.user_password_hasher');
        $hashedPassword = $hasher->hashPassword($user, 'Password123!');
        $user->setPassword($hashedPassword);

        $dm->flush();
        $dm->clear(); // On vide la mémoire de Doctrine pour forcer une nouvelle lecture au login

        // Tenter la connexion via le fomulaire
        $crawler = $client->request('GET', '/login');

        // Remplace par un utilisateur existant dans la base de test
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@eco-tracker.re',
            'password' => 'Password123!',
        ]);

        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé vers son profil après succès
        $this->assertResponseRedirects('/me');

        $client->followRedirect();
        $this->assertResponseIsSuccessful(); // Code 200
        $this->assertSelectorTextContains('h1', 'Mes informations personnelles');
    }
}
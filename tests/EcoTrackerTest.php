<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EcoTrackerTest extends WebTestCase
{
    // Test basique pour vérifier que la page d'accueil est accessible
    public function testHomePageIsUp(): void
    {
        $client = static::createClient();
        // On tente d'accéder à la liste
        $crawler = $client->request('GET', '/eco/list');

        // Vérifie que la page répond bien (Code HTTP 200)
        $this->assertResponseIsSuccessful();
        // Vérifie qu'on a bien notre titre
        $this->assertSelectorTextContains('h1', 'Communauté Eco-Tracker');
    }

    // Test pour vérifier que le formulaire de création d'utilisateur fonctionne
    public function testCreateUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/new');

        // On remplit le formulaire avec des données de test
        $buttonCrawlerNode = $crawler->selectButton('Créer mon profil');
        $form = $buttonCrawlerNode->form([
            'user[username]' => 'TesteurBio',
            'user[email]'    => 'test@eco.com',
            'user[password]' => 'Password123!',
        ]);

        $client->submit($form);

        // Après soumission, on doit être redirigé vers la liste
        $this->assertResponseRedirects('/eco/list');
        $client->followRedirect();
        
        // On vérifie que le message de succès s'affiche
        $this->assertSelectorExists('.alert-success');
    }
}

<?php

namespace App\Controller;

use App\Document\User;
use App\Document\EcoAction;
use App\Form\UserType;
use App\Form\EcoActionType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EcoTrackerController extends AbstractController
{
    #[Route('/user/new', name: 'user_new')]
    public function new(
        Request $request, 
        DocumentManager $dm,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // HACHAGE DU MOT DE PASSE
            $hashedPassword = $hasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Le formulaire a rempli l'objet $user automatiquement
            $dm->persist($user);
            $dm->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');

            return $this->redirectToRoute('eco_list');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    } 

    #[Route('/user/{id}/add-action', name: 'user_add_action', requirements: ['id' => '[a-f0-9-]{36}'])]
    public function addAction(
        string $id, 
        Request $request, 
        DocumentManager $dm,
    ): Response {
        // On récupère l'utilisateur ciblé
        $user = $dm->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // On prépare l'action
        $action = new EcoAction();
        $form = $this->createForm(EcoActionType::class, $action);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // LE LIEN : On ajoute l'action à l'utilisateur
            $user->addAction($action);

            // On enregistre
            $dm->persist($action);
            $dm->flush();

            $this->addFlash('success', 'Action ajoutée pour ' . $user->getUsername());

            return $this->redirectToRoute('eco_list');
        }

        return $this->render('eco_tracker/add_action.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/eco/add', name: 'eco_add')]
    public function add(DocumentManager $dm): Response
    {
         // Création de l'utilisateur
        $user = new User();
        $user->setUsername("Vivien_" . uniqid()); // Uniqid pour éviter les doublons
        $user->setEmail("vivien@example.com");

        // Création d'une action
        $action = new EcoAction();
        $action->setTitle("Trajet en vélo");
        $action->setCarbonSaved(500);

        $user->addAction($action);

        // Enregistrement (le cascade="all" s'occupe de l'action)
        $dm->persist($user);

        // Validation en base
        $dm->flush();

        return $this->json(['status' => 'Success!']);    
    }

    #[Route('/eco/list', name: 'eco_list')]
    public function list(DocumentManager $dm): Response
    {
        // Récupérer tous les utilisateurs
        $users = $dm->getRepository(User::class)->findAll();

        return $this->render('eco_tracker/list.html.twig', [
            'users' => $users,
        ]);

    }

    #[Route('/observatoire/carte', name: 'app_map')]
    public function showMap(DocumentManager $dm, HttpClientInterface $httpClient): Response
    {
        $users = $dm->getRepository(User::class)->findAll();

        // On calcule le nombre de gestes par commune
        $statsCommunes = [];
        foreach ($users as $user) {
            $nomCommune = $user->getCommune();
            if ($nomCommune) {
                if (!isset($statsCommunes[$nomCommune])) {
                    $statsCommunes[$nomCommune] = 0;
                }
                // On ajoute le nombre d'actions de cet utilisateur au total de la commune
                $statsCommunes[$nomCommune] += count($user->getActions());
            }
        }

        // On récupère les contours des communes du 974 via Etalab (Source OSM/IGN)
        $response = $httpClient->request(
            'GET',
            'https://geo.api.gouv.fr/departements/974/communes?format=geojson&geometry=contour'
        );
        
        $communesGeoJson = $response->getContent();

        return $this->render('map/index.html.twig', [
            'users' => $users,
            'communes_geojson' => $communesGeoJson,
            'stats_communes' => json_encode($statsCommunes)
        ]);
    }
}
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

class EcoTrackerController extends AbstractController
{
    #[Route('/user/new', name: 'user_new')]
    public function new(Request $request, DocumentManager $dm): Response
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

    #[Route('/user/{id}/add-action', name: 'user_add_action')]
    public function addAction(
        string $id, 
        Request $request, 
        DocumentManager $dm
    ): Response {
        // 1. On récupère l'utilisateur ciblé
        $user = $dm->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // 2. On prépare l'action
        $action = new EcoAction();
        $form = $this->createForm(EcoActionType::class, $action);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 3. LE LIEN : On ajoute l'action à l'utilisateur
            // (Assure-toi d'avoir la méthode addAction dans ton Document User)
            $user->addAction($action);

            // 4. On enregistre
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

        // On prépare un affichage simple (en attendant d'utiliser Twig)
        // $html = "<h1>Liste des Eco-Trackers</h1>";
        // foreach ($users as $user) {
        //     $html .= "<h2>Utilisateur : " . $user->getUsername() . "</h2>";
        //     $html .= "<ul>";
        //     foreach ($user->getActions() as $action) {
        //         $html .= "<li>Action : " . $action->getTitle() . " (" . $action->getCarbonSaved() . "g CO2 sauvés)</li>";
        //     }
        //     $html .= "</ul>";
        // }

        // return new Response($html);
    }
}
<?php
namespace App\Controller;

use App\Document\EcoProduct;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-mongo', name: 'test_mongo')]
    public function test(DocumentManager $dm): Response
    {
        $product = new EcoProduct();
        $product->setName("Gourde Eco-Tracker");
        $product->setCarbonScore(150);

        $dm->persist($product);
        $dm->flush();

        return new Response("Succès ! Produit enregistré avec l'ID : " . $product->getId());
    }
}
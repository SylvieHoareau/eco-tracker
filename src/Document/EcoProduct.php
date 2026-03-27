<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Uid\Uuid;

#[MongoDB\Document]
class EcoProduct
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type:"string")]
    private string $name;

    #[MongoDB\Field(type:"int")]
    private $carbonScore;

    public function __construct()
    {
        // Génération automatique d'un identifiant unique
        // $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string { return $this->id; }
    public function setName(string $name): void { $this->name = $name; }
    public function getName(): ?string { return $this->name; }
    public function setCarbonScore(int $score): void { $this->carbonScore = $score; }
}
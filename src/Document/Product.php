<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Uid\Uuid;

#[MongoDB\Document]
class Product
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: 'string')]
    private string $name;

    public function __construct()
    {
        // Génération automatique d'un identifiant unique
        // $this->id = Uuid::v4()->toRfc4122();
    }

    // Getters et Setters...
    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
}
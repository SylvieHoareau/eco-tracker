<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Uid\Uuid;

#[MongoDB\Document]
class EcoAction
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private ?string $title = null;

    #[MongoDB\Field(type: "int")]
    private int $carbonSaved = 0; // en grammes par exemple

    #[MongoDB\ReferenceOne(targetDocument: User::class, inversedBy: "actions")]
    private ?User $user = null;

    public function __construct()
    {
        // Génération automatique d'un identifiant unique
        $this->id = Uuid::v4()->toRfc4122();
    }

    // Getters et Setters...
    public function getId(): ?string { 
        return $this->id; 
    }

    public function getTitle(): ?string { 
        return $this->title; 
    }

    public function setTitle(string $title): self { 
        $this->title = $title; 
        return $this; 
    }

    public function getCarbonSaved(): int { 
        return $this->carbonSaved; 
    }

    public function setCarbonSaved(int $carbonSaved): self {
         $this->carbonSaved = $carbonSaved; 
         return $this; 
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;
        return $this;
    }
}
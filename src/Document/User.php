<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "users")] // Nom de la collection en base
class User
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private ?string $username = null;

    #[MongoDB\Field(type: "string")]
    private ?string $email = null;

    // RELATION : Un utilisateur peut avoir plusieurs EcoActions
    #[MongoDB\ReferenceMany(
        targetDocument: EcoAction::class, 
        cascade: ["persist", "remove"] // Permet de persister/supprimer les actions en même temps que l'utilisateur
    )]
    private Collection $actions;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        // $this->createdAt = new \DateTime(); // Date de création par défaut
    }

    // Getters / Setters
    public function getId(): ?string { 
        return $this->id; 
    }

    public function getUsername(): ?string { 
        return $this->username; 
    }

    public function setUsername(string $username): self { 
        $this->username = $username; return $this; 
    }

    public function getEmail(): ?string { 
        return $this->email; 
    }

    public function setEmail(string $email): self { 
        $this->email = $email; return $this; 
    }

    public function getActions(): Collection { 
        return $this->actions; 
    }

    public function addAction(EcoAction $action): self
    {        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }
        return $this;    
    }

    public function removeAction(EcoAction $action): self
    {        $this->actions->removeElement($action);
        return $this;    
    }

    public function getCreatedAt(): ?\DateTimeInterface { 
        return $this->createdAt; 
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self { 
        $this->createdAt = $createdAt; 
        return $this;
    }
}
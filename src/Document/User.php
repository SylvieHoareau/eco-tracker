<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[MongoDB\Document(collection: "users")] // Nom de la collection en base
#[MongoDB\UniqueIndex(keys: ["email" => "asc"])] // Index MongoDB pour l'unicité
#[MongoDB\UniqueIndex(keys: ["username" => "asc"])]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[MongoDB\Id(strategy: "NONE", type: "string")]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire")]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $username = null;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Email(message: "L'email {{ value }} n'est pas un email valide")]
    private ?string $email = null;

    #[MongoDB\Field(type: "string")]
    private ?string $password = null;

    #[MongoDB\Field(type: "collection")]
    private array $roles = [];

    // RELATION : Un utilisateur peut avoir plusieurs EcoActions
    #[MongoDB\ReferenceMany(
        targetDocument: EcoAction::class, 
        cascade: ["all", "remove"], // Permet de persister/supprimer les actions en même temps que l'utilisateur
        storeAs: "id"
    )]
    private Collection $actions;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Veuillez sélectionner votre commune")]
    private ?string $commune = null;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        // On génère l'UUID à la création de l'objet
        $this->id = Uuid::v4()->toRfc4122();
        $this->actions = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->roles = ['ROLE_USER']; // Rôle par défaut
    }

    // --- Méthodes obligatoires pour la sécurité Symfony ---

    public function getUserIdentifier(): string {
        return (string) $this->email; // On se connecte avec l'email
    }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Garantit que chaque utilisateur a au moins ce rôle
        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {
        // Utilisé pour effacer des données sensibles temporaires (si nécessaire)
    }

    // Getters / Setters
    public function getId(): ?string { 
        return (string) $this->id; 
    }

    public function getUsername(): ?string { 
        return (string) $this->username; 
    }

    public function setUsername(string $username): self { 
        $this->username = $username; return $this; 
    }

    public function getEmail(): ?string { 
        return (string) $this->email; 
    }

    public function setEmail(string $email): self { 
        $this->email = $email; return $this; 
    }

    public function getActions(): Collection { 
        return $this->actions; 
    }

    public function addAction(EcoAction $action): self
    {        
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setUser($this); // Assure la relation inverse si tu as un champ "user" dans EcoAction
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

    public function getCommune(): ?string { 
        return $this->commune; 
    }

    public function setCommune(?string $commune): self { 
        $this->commune = $commune; return $this; 
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self { 
        $this->createdAt = $createdAt; 
        return $this;
    }



}
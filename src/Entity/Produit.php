<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[UniqueEntity('reference', message: "La référence existe déjà !")]

class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[NotBlank(message: "nom obligatoire!")]
    #[Assert\Length(max: 50, maxMessage: "Le nom doit contenir au plus 50 caractères !")]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    #[NotBlank(message: "La référence est obligatoire !")]
    #[Assert\Length(max: 20, maxMessage: "La référence doit contenir au plus 20 caractères !")]
    private ?string $reference = null;

    #[ORM\Column(length: 50)]
    #[NotBlank(message: "Le type de conditionnement est obligatoire !")]
    #[Assert\Length(max: 30, maxMessage: "Le type de conditionnement doit contenir au plus 30 caractères !")]
    private ?string $typeConditionnement = null;

    #[ORM\Column]
    #[NotBlank(message: "La quantité est obligatoire !")]
    #[Assert\Positive(message: "La quantité doit être supérieure ou égale à 1 !")]
    private ?int $quantite = null;

    #[ORM\Column(length: 40)]
    #[NotBlank(message: "L'emplacement est obligatoire !")]
    #[Assert\Length(max: 30, maxMessage: "L'emplacement doit contenir au plus 30 caractères !")]
    private ?string $emplacement = null;

    #[ORM\Column]
    #[NotBlank(message: "Le prix est obligatoire !")]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou nul !")]
    private ?float $prix = null;

    #[ORM\Column]
    #[NotBlank(message: "Le quota est obligatoire !")]
    #[Assert\PositiveOrZero(message: "Le quota doit être supérieur à 0 ou nul !")]
    private ?int $quota = null;

    #[ORM\Column]
    #[NotBlank(message: "Le stock est obligatoire !")]
    #[Assert\PositiveOrZero(message: "Le stock doit être supérieur à 0 ou nul !")]
    private ?int $stock = null;

    #[ORM\Column]
    private ?bool $estActif = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMaj = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTypeConditionnement(): ?string
    {
        return $this->typeConditionnement;
    }

    public function setTypeConditionnement(string $typeConditionnement): static
    {
        $this->typeConditionnement = $typeConditionnement;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(int $quota): static
    {
        $this->quota = $quota;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): static
    {
        $this->estActif = $estActif;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(\DateTimeInterface $dateMaj): static
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }
}

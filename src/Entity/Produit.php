<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[UniqueEntity('reference', message: "La référence existe déjà !")]
#[Vich\Uploadable]

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
    private ?int $prix = null;

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

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseur = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'produit')]
    private Collection $lignesCommandes;

    public function __construct()
    {
        $this->lignesCommandes = new ArrayCollection();
    }


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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->dateMaj = new DateTime();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignesCommandes(): Collection
    {
        return $this->lignesCommandes;
    }

    public function addLignesCommande(LigneCommande $lignesCommande): static
    {
        if (!$this->lignesCommandes->contains($lignesCommande)) {
            $this->lignesCommandes->add($lignesCommande);
            $lignesCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLignesCommande(LigneCommande $lignesCommande): static
    {
        if ($this->lignesCommandes->removeElement($lignesCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignesCommande->getProduit() === $this) {
                $lignesCommande->setProduit(null);
            }
        }

        return $this;
    }
}

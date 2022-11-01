<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // -- Validtaion en annotations --
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le nom du produit doit contenir au moins 3 caractères')]
    private ?string $name = null;

    #[ORM\Column]
    // -- Validtaion en annotations --
    #[Assert\NotBlank(message: 'Le prix du produit est obligatoire')]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: 'La photo principale doit être une url valide')]
    #[Assert\NotBlank(message: 'La photo principale est obligatoire')]
    private ?string $picture = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description courte doit être obligatoire')]
    #[Assert\Length(min: 20, minMessage:'La description courte doit au minimum faire 20 caractères')]
    private ?string $shortDescription = null;


    // -- validate les données en static dans la classe du produit --

    // public static function loadValidatorMetadata(ClassMetadata $metadata) {
    //     $metadata->addPropertyConstraints('name', [
    //         new Assert\NotBlank(['message' => 'Le nom du produit est obligatoire']),
    //         new Assert\Length(['min' => 3, 'max' => 255, 'minMessage' => 'Le nom du produit doit contenir au moins 3 caractères'])
    //     ]);
    //     $metadata->addPropertyConstraint('price', new Assert\NotBlank(['message' => 'Le prix du produit est obligatoire']));
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}

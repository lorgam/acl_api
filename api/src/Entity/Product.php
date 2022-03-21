<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @Groups({"rest"})
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"rest"})
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @Groups({"rest"})
     *
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;

    /**
     * @Groups({"rest"})
     *
     * @ORM\Column(type="float")
     * @Assert\NotNull
     */
    private $price;

    /**
     * @Groups({"rest"})
     *
     * @ORM\Column(type="string", length=3)
     * @CustomAssert\ValidCurrency
     */
    private $currency;

    /**
     * @Groups({"rest"})
     *
     * @ORM\Column(type="boolean")
     * @Assert\NotNull
     */
    private $featured;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }
}

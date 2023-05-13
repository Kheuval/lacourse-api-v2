<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ListDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ListDetailRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['listDetail:read']],
    denormalizationContext: ['groups' => ['listDetail:write']],
)]
class ListDetail
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'ingredient:read',
        'ingredient:write',
        'listDetail:write',
        'groceryList:write'
    ])]
    #[NotBlank]
    private ?string $unit = null;

    #[ORM\Column(type: 'float')]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'ingredient:read',
        'ingredient:write',
        'listDetail:write',
        'groceryList:write'
    ])]
    #[NotBlank]
    private ?float $quantity = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'ingredient:read',
        'ingredient:write',
        'listDetail:write',
        'groceryList:write'
    ])]
    private bool $isActive = true;

    #[ORM\ManyToOne(targetEntity: GroceryList::class, inversedBy: 'listDetails'), ORM\JoinColumn(nullable: false)]
    #[Groups([
        'listDetail:read',
        'ingredient:read',
        'listDetail:write',
    ])]
    #[NotBlank]
    private ?GroceryList $groceryList = null;

    #[
        ORM\ManyToOne(targetEntity: Ingredient::class, cascade: ['persist'], inversedBy: 'listDetails'),
        ORM\JoinColumn(nullable: false),
    ]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'ingredient:read',
        'listDetail:write',
        'groceryList:write'
    ])]
    #[NotBlank]
    private ?Ingredient $ingredient = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'listDetails')]
    #[Groups([
        'listDetail:read',
        'listDetail:write',
        'groceryList:read',
    ])]
    private ?Recipe $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getGroceryList(): ?GroceryList
    {
        return $this->groceryList;
    }

    public function setGroceryList(?GroceryList $groceryList): self
    {
        $this->groceryList = $groceryList;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: RecipeIngredientRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
    ],
    normalizationContext: ['groups' => ['recipeIngredient:read']],
    denormalizationContext: ['groups' => ['recipeIngredient:write']],
)]
class RecipeIngredient
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipeIngredient:write',
        'recipe:write',
        'user:read',
    ])]
    #[NotBlank]
    private ?string $unit = null;

    #[ORM\Column(type: 'float')]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipeIngredient:write',
        'recipe:write',
        'user:read',
    ])]
    #[NotBlank]
    private ?float $quantity = null;

    #[
        ORM\ManyToOne(
            targetEntity: Ingredient::class,
            cascade: ['persist'],
            inversedBy: 'recipeIngredients',
        ),
        ORM\JoinColumn(nullable: false)
    ]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'recipeIngredient:write',
        'recipe:write',
        'user:read',
    ])]
    #[NotBlank]
    private ?Ingredient $ingredient = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'recipeIngredients')]
    #[Groups([
        'recipeIngredient:read',
        'ingredient:read',
        'recipeIngredient:write'
    ])]
    #[NotBlank]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

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

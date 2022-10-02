<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['ingredient:read']],
    denormalizationContext: ['groups' => ['ingredient:write']],
    paginationItemsPerPage: 7,
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['name' => SearchFilterInterface::STRATEGY_START],
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: ['isEdible'],
)]
class Ingredient
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups([
        'recipeIngredient:read',
        'listDetail:read',
        'recipe:read',
        'groceryList:read',
        'ingredient:read',
    ])]
    #[NotBlank]
    #[ApiProperty(writable: false)]
    private int $id;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[NotBlank]
    #[Groups([
        'recipeIngredient:read',
        'listDetail:read',
        'recipe:read',
        'groceryList:read',
        'ingredient:read',
        'recipeIngredient:write',
        'listDetail:write',
        'recipe:write',
        'groceryList:write',
        'ingredient:write'
    ])]
    private string $name;

    #[ORM\OneToMany(
        mappedBy: 'ingredient',
        targetEntity: RecipeIngredient::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private ArrayCollection $recipeIngredients;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'listDetail:write',
        'groceryList:write',
        'ingredient:read',
    ])]
    private bool $isEdible;

    #[ORM\OneToMany(
        mappedBy: 'ingredient',
        targetEntity: ListDetail::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private ArrayCollection $listDetails;

    public function __construct()
    {
        $this->recipeIngredients = new ArrayCollection();
        $this->listDetails = new ArrayCollection();
    }

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

    public function getRecipeIngredients(): Collection
    {
        return $this->recipeIngredients;
    }

    public function addRecipeIngredient(RecipeIngredient $recipeIngredient): self
    {
        if (!$this->recipeIngredients->contains($recipeIngredient)) {
            $this->recipeIngredients[] = $recipeIngredient;
            $recipeIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient): self
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            if ($recipeIngredient->getIngredient() === $this) {
                $recipeIngredient->setIngredient(null);
            }
        }

        return $this;
    }

    public function getIsEdible(): ?bool
    {
        return $this->isEdible;
    }

    public function setIsEdible(bool $isEdible): self
    {
        $this->isEdible = $isEdible;

        return $this;
    }

    public function getListDetails(): Collection
    {
        return $this->listDetails;
    }

    public function addListDetail(ListDetail $listDetail): self
    {
        if (!$this->listDetails->contains($listDetail)) {
            $this->listDetails[] = $listDetail;
            $listDetail->setIngredient($this);
        }

        return $this;
    }

    public function removeListDetail(ListDetail $listDetail): self
    {
        if ($this->listDetails->removeElement($listDetail)) {
            if ($listDetail->getIngredient() === $this) {
                $listDetail->setIngredient(null);
            }
        }

        return $this;
    }
}

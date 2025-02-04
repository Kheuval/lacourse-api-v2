<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\RecipePendingAction;
use App\Controller\RecipeSampleAction;
use App\Repository\RecipeRepository;
use App\StateProcessor\Recipe\CreateRecipeProcessor;
use App\StateProvider\RecipeProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: RecipeProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/recipes/sample',
            controller: RecipeSampleAction::class,
            paginationEnabled: false,
            security: "is_granted('ROLE_USER')",
            read: false,
        ),
        new GetCollection(
            uriTemplate: '/recipes/pending',
            controller: RecipePendingAction::class,
            paginationEnabled: false,
            security: "is_granted('ROLE_ADMIN')",
            read: false,
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            processor: CreateRecipeProcessor::class,
        ),
        new Get(
            security: "is_granted('ROLE_USER')",
            provider: RecipeProvider::class,
        ),
        new Patch(
            security: "object.getUser() == user || is_granted('ROLE_ADMIN')",
            processor: CreateRecipeProcessor::class,
        ),
        new Delete(
            security: 'object.getUser() == user',
        ),
    ],
    normalizationContext: ['groups' => ['recipe:read']],
    denormalizationContext: ['groups' => ['recipe:write']],
    paginationItemsPerPage: 7,
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['name' => SearchFilterInterface::STRATEGY_PARTIAL],
)]
class Recipe
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'user:read',
        'ingredient:write',
        'recipe:write'
    ])]
    #[NotBlank]
    private string $name;

    #[ORM\ManyToOne(targetEntity: MediaObject::class), ORM\JoinColumn(nullable: false)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'user:read',
        'ingredient:write',
        'recipe:write'
    ])]
    private ?MediaObject $image = null;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipe:write',
        'user:read',
    ])]
    private int $servings = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipe:write',
        'user:read',
    ])]
    #[NotBlank]
    private int $totalTime;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipe:write',
        'user:read',
    ])]
    private int $preparationTime = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipe:write',
        'user:read',
    ])]
    private int $restTime = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'recipeIngredient:read',
        'recipe:read',
        'ingredient:read',
        'ingredient:write',
        'recipe:write',
        'user:read',
    ])]
    private int $cookingTime = 0;

    #[ORM\OneToMany(
        mappedBy: 'recipe',
        targetEntity: RecipeIngredient::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[Groups([
        'recipe:read',
        'ingredient:read',
        'recipe:write',
        'ingredient:write',
        'user:read',
    ])]
    private Collection $recipeIngredients;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'recipes'), ORM\JoinColumn(nullable: true)]
    #[Groups([
        'recipe:read',
    ])]
    private User $user;

    #[ORM\OneToMany(
        mappedBy: 'recipe',
        targetEntity: Step::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[Groups([
        'recipe:read',
        'recipe:write',
        'user:read',
    ])]
    private Collection $steps;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: ListDetail::class)]
    private Collection $listDetails;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'recipe:read',
        'recipe:write',
    ])]
    private ?bool $verified = null;

    public function __construct()
    {
        $this->recipeIngredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
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

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(MediaObject $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getServings(): ?int
    {
        return $this->servings;
    }

    public function setServings(?int $servings): self
    {
        $this->servings = $servings;

        return $this;
    }

    public function getTotalTime(): ?int
    {
        return $this->totalTime;
    }

    public function setTotalTime(int $totalTime): self
    {
        $this->totalTime = $totalTime;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(?int $preparationTime): self
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getRestTime(): ?int
    {
        return $this->restTime;
    }

    public function setRestTime(?int $restTime): self
    {
        $this->restTime = $restTime;

        return $this;
    }

    public function getCookingTime(): ?int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(?int $cookingTime): self
    {
        $this->cookingTime = $cookingTime;

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
            $recipeIngredient->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient): self
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            if ($recipeIngredient->getRecipe() === $this) {
                $recipeIngredient->setRecipe(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

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
            $listDetail->setRecipe($this);
        }

        return $this;
    }

    public function removeListDetail(ListDetail $listDetail): self
    {
        if ($this->listDetails->removeElement($listDetail)) {
            if ($listDetail->getRecipe() === $this) {
                $listDetail->setRecipe(null);
            }
        }

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(?bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }
}

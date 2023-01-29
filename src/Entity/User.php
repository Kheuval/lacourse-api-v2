<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use App\StateProcessor\User\CreateUserProcessor;
use App\StateProcessor\User\UpdateUserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            processor: CreateUserProcessor::class
        ),
        new GetCollection(security: 'object == user'),
        new Get(security: 'object == user'),
        new Patch(
            denormalizationContext: ['groups' => 'user:update'],
            security: 'object == user',
            processor: UpdateUserProcessor::class
        ),
        new Delete(security: 'object == user'),
        new Put(security: 'object == user')
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
#[UniqueEntity('email')]
#[UniqueEntity('username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:read',
        'user:write'
    ])]
    #[NotBlank]
    private string $email;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:read',
        'user:write',
        'recipe:read'
    ])]
    #[NotBlank]
    private string $username;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[SerializedName('password')]
    #[Groups([
        'user:write',
        'user:update'
    ])]
    #[NotBlank]
    private ?string $plainPassword;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Recipe::class,
        orphanRemoval: true,
    )]
    #[Groups([
        'user:read',
        'recipe:write'
    ])]
    private Collection $recipes;

    #[ORM\ManyToMany(targetEntity: Recipe::class), ORM\JoinTable(name: 'favorite_list')]
    #[Groups([
        'user:read',
    ])]
    private Collection $favorites;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: GroceryList::class,
        orphanRemoval: true,
    )]
    #[Groups([
        'user:read',
        'groceryList:read',
        'groceryList:write'
    ])]
    private Collection $groceryLists;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->groceryLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }


    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            if ($recipe->getUser() === $this) {
                $recipe->setUser(null);
            }
        }

        return $this;
    }

    public function getFavorites(): Collection|array
    {
        return $this->favorites;
    }

    public function addFavorite(Recipe $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(Recipe $favorite): self
    {
        $this->favorites->removeElement($favorite);

        return $this;
    }

    public function getGroceryLists(): Collection|array
    {
        return $this->groceryLists;
    }

    public function addGroceryList(GroceryList $groceryList): self
    {
        if (!$this->groceryLists->contains($groceryList)) {
            $this->groceryLists[] = $groceryList;
            $groceryList->setUser($this);
        }

        return $this;
    }

    public function removeGroceryList(GroceryList $groceryList): self
    {
        if ($this->groceryLists->removeElement($groceryList)) {
            if ($groceryList->getUser() === $this) {
                $groceryList->setUser(null);
            }
        }

        return $this;
    }
}

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
use App\Repository\GroceryListRepository;
use App\StateProcessor\GroceryList\CreateGroceryListProcessor;
use App\StateProvider\GroceryListProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: GroceryListRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: GroceryListProvider::class,
        ),
        new Post(
            processor: CreateGroceryListProcessor::class,
        ),
        new Get(
            security: 'object.getUser() == user',
        ),
        new Patch(
            security: 'object.getUser() == user',
            processor: CreateGroceryListProcessor::class,
        ),
        new Delete(
            security: 'object.getUser() == user',
        ),
        new Put(
            security: 'object.getUser() == user',
        )
    ],
    normalizationContext: ['groups' => ['groceryList:read']],
    denormalizationContext: ['groups' => ['groceryList:write']],
)]
class GroceryList
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'user:read',
        'listDetail:write',
        'groceryList:write'
    ])]
    #[NotBlank]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'listDetail:read',
        'groceryList:read',
        'user:read',
        'listDetail:write',
        'groceryList:write',
    ])]
    private bool $isActive = true;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'groceryLists'), ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\OneToMany(
        mappedBy: 'groceryList',
        targetEntity: ListDetail::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[Groups([
        'groceryList:read',
        'ingredient:read',
        'groceryList:write',
        'ingredient:write',

    ])]
    private Collection $listDetails;

    public function __construct()
    {
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    public function getListDetails(): Collection
    {
        return $this->listDetails;
    }

    public function addListDetail(ListDetail $listDetail): self
    {
        if (!$this->listDetails->contains($listDetail)) {
            $this->listDetails[] = $listDetail;
            $listDetail->setGroceryList($this);
        }

        return $this;
    }

    public function removeListDetail(ListDetail $listDetail): self
    {
        if ($this->listDetails->removeElement($listDetail)) {
            if ($listDetail->getGroceryList() === $this) {
                $listDetail->setGroceryList(null);
            }
        }

        return $this;
    }
}

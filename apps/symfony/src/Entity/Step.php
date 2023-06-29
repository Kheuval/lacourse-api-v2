<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\StepRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: StepRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
    ],
    normalizationContext: ['groups' => ['step:read']],
    denormalizationContext: ['groups' => ['step:write']],
)]
class Step
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    #[Groups([
        'step:read',
        'recipe:read',
        'recipe:write',
        'user:read',
    ])]
    #[NotBlank]
    private ?string $stepDescription = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'steps')]
    #[NotBlank]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStepDescription(): ?string
    {
        return $this->stepDescription;
    }

    public function setStepDescription(string $stepDescription): self
    {
        $this->stepDescription = $stepDescription;

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

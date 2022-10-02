<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateImageAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            controller: CreateImageAction::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'image' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                        'description' => 'L\'image à télécharger'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            security: 'is_granted("ROLE_USER")',
            deserialize: false
        ),
        new Get(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['image:read']]
)]
class Image
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups([
        'image:read'
    ])]
    #[NotBlank]
    private int $id;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups([
        'image:read'
    ])]
    public ?string $contentUrl = null;

    #[UploadableField(mapping: 'image', fileNameProperty: 'filePath')]
    public ?File $file = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private string $filePath;

    public function getId(): ?int
    {
        return $this->id;
    }
}

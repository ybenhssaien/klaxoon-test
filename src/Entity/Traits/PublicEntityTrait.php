<?php

namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="PublicResource")
 */
trait PublicEntityTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36)
     *
     * @Groups({"read"})
     *
     * @OA\Property(
     *     type="string",
     *     description="The resource identifier"
     * )
     */
    protected ?string $id;

    public function getId(): ?string
    {
        return $this->id;
    }
}
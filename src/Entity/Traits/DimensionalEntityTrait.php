<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="DimensionalResourceRead",
 *     @OA\Property(property="height", type="number", description="The media height in pixels"),
 *     @OA\Property(property="width", type="number", description="The media width in pixels"),
 * )
 * @OA\Schema(
 *     schema="DimensionalResourceUpdate",
 *     ref="#/components/schemas/DimensionalResourceRead",
 * )
 */

trait DimensionalEntityTrait
{
    /**
     * @var null|float in pixels by default
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"read", "edit"})
     */
    protected ?float $height = null;

    /**
     * @var null|float in pixels by default
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"read", "edit"})
     */
    protected ?float $width = null;

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }
}
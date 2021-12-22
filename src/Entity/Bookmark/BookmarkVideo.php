<?php

namespace App\Entity\Bookmark;

use App\Entity\Bookmark;
use App\Entity\Traits\DimensionalEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity
 *
 * @OA\Schema(
 *     schema="BookmarkVideoRead",
 *     allOf={
 *      @OA\Schema(ref="#/components/schemas/DimensionalResourceRead")
 *     },
 *     @OA\Property(property="duration", type="number", description="The video duration in seconds"),
 * )
 * @OA\Schema(
 *     schema="BookmarkVideoUpdate",
 *     allOf={
 *      @OA\Schema(ref="#/components/schemas/DimensionalResourceUpdate")
 *     },
 *     @OA\Property(property="duration", type="number", description="The video duration in seconds"),
 * )
 */
class BookmarkVideo extends Bookmark
{
    use DimensionalEntityTrait;

    protected string $type = self::BOOKMARK_VIDEO;

    /**
     * @var null|float in seconds by default
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"read", "edit"})
     */
    protected ?float $duration = null;

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * @param float|null $duration duration in seconds
     */
    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
<?php

namespace App\Entity\Bookmark;

use App\Entity\Bookmark;
use App\Entity\Traits\DimensionalEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity
 *
 * @OA\Schema(
 *     schema="BookmarkImageRead",
 *     allOf={
 *      @OA\Schema(ref="#/components/schemas/DimensionalResourceRead")
 *     }
 * )
 * @OA\Schema(
 *     schema="BookmarkImageUpdate",
 *     allOf={
 *      @OA\Schema(ref="#/components/schemas/DimensionalResourceUpdate")
 *     }
 * )
 */
class BookmarkImage extends Bookmark
{
    use DimensionalEntityTrait;

    protected string $type = self::BOOKMARK_IMAGE;
}
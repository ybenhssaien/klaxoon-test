<?php

namespace App\Entity;

use App\Entity\Traits\PublicEntityTrait;
use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @ORM\Table(
 *     name="tags",
 *     indexes={
 *      @ORM\Index(name="tag_idx", columns={"name"})
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="TagRead",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/PublicResource"),
 *          @OA\Schema(ref="#/components/schemas/TagUpdate"),
 *     },
 * )
 * @OA\Schema(
 *     schema="TagUpdate",
 *     @OA\Property(property="name", type="string", description="The tag keyword"),
 * )
 */
class Tag
{
    use PublicEntityTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read", "write"})
     *
     * @Assert\NotBlank(message="The tag name should not be blank")
     */
    protected string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Bookmark::class, inversedBy="tags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookmark;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public static function createFromName(string $name): self
    {
        return new static($name);
    }

    public function getBookmark(): ?Bookmark
    {
        return $this->bookmark;
    }

    public function setBookmark(?Bookmark $bookmark): self
    {
        $this->bookmark = $bookmark;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}

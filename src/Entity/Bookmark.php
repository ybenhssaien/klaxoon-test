<?php

namespace App\Entity;

use App\Entity\Traits\PublicEntityTrait;
use App\Entity\Traits\UpdatableEntityTrait;
use App\Repository\BookmarkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Bookmark\BookmarkImage;
use App\Entity\Bookmark\BookmarkVideo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=BookmarkRepository::class)
 * @ORM\Table(name="bookmarks")
 * @ORM\HasLifecycleCallbacks()
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn("type", type="string", length=10)
 * @ORM\DiscriminatorMap({
 *     Bookmark::BOOKMARK_MIXED = Bookmark::class,
 *     Bookmark::BOOKMARK_IMAGE = BookmarkImage::class,
 *     Bookmark::BOOKMARK_VIDEO = BookmarkVideo::class,
 * })
 *
 * @OA\Schema(
 *     schema="BookmarkCreate",
 *     @OA\Property(property="bookmark-url", type="string", description="The url to bookmark"),
 * )
 * @OA\Schema(
 *     schema="BookmarkUpdate",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/BookmarkImageUpdate"),
 *          @OA\Schema(ref="#/components/schemas/BookmarkVideoUpdate"),
 *     },
 *     @OA\Property(property="url", type="string", description="The main url (video, image, ..) in the bookmarked page"),
 *     @OA\Property(property="title", type="string", description="The title of bookmarked page"),
 *     @OA\Property(property="author", type="string", description="The author of bookmarked page"),
 * )
 * @OA\Schema(
 *     schema="BookmarkRead",
 *     oneOf={
 *          @OA\Schema(ref="#/components/schemas/BookmarkImageRead"),
 *          @OA\Schema(ref="#/components/schemas/BookmarkVideoRead"),
 *     },
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/BookmarkCreate"),
 *          @OA\Schema(ref="#/components/schemas/PublicResource"),
 *          @OA\Schema(ref="#/components/schemas/UpdatableResource"),
 *     },
 *     @OA\Property(property="url", type="string", description="The main url (video, image, ..) in the bookmarked page"),
 *     @OA\Property(property="title", type="string", description="The title of bookmarked page"),
 *     @OA\Property(property="author", type="string", description="The author of bookmarked page"),
 *     @OA\Property(property="tags", type="array", description="The list of keywords to identify the bookmarked page", @OA\Items(ref="#/components/schemas/TagRead")),
 * )
 */
class Bookmark
{
    use PublicEntityTrait;
    use UpdatableEntityTrait;

    const BOOKMARK_MIXED = 'mixed';
    const BOOKMARK_IMAGE = 'image';
    const BOOKMARK_VIDEO = 'video';

    const BOOKMARK_TYPES = [
        self::BOOKMARK_MIXED => self::BOOKMARK_MIXED,
        self::BOOKMARK_IMAGE => self::BOOKMARK_IMAGE,
        self::BOOKMARK_VIDEO => self::BOOKMARK_VIDEO,
    ];

    protected string $type = self::BOOKMARK_MIXED;

    /**
     * @ORM\Column(type="string", length=1000)
     *
     * @Groups({"read", "create"})
     * @SerializedName("bookmark-url")
     *
     * @Assert\Url(message="The provided bookmark url is not a valid URL.")
     * @Assert\NotBlank
     */
    protected ?string $bookmarkUrl;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     *
     * @Groups({"read", "edit"})
     *
     * @Assert\Url(message="The bookmark's content url is not a valid URL.")
     */
    protected ?string $url = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"read", "edit"})
     */
    protected ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"read", "edit"})
     */
    protected ?string $author = null;

    /**
     * @ORM\OneToMany(targetEntity=Tag::class, mappedBy="bookmark", orphanRemoval=true, cascade={"remove", "persist"})
     *
     * @Groups({"read", "create"})
     */
    private Collection $tags;

    public function __construct(?string $bookmarkUrl = null)
    {
        if ($bookmarkUrl) {
            $this->setBookmarkUrl($bookmarkUrl);
        }
        $this->tags = new ArrayCollection();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (! isset(self::BOOKMARK_TYPES[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot find bookmark type "%s", available types are "%s"',
                $type,
                implode('", "', self::BOOKMARK_TYPES)
            ));
        }

        $this->type = $type;

        return $this;
    }

    public function getBookmarkUrl(): ?string
    {
        return $this->bookmarkUrl;
    }

    public function setBookmarkUrl(string $bookmarkUrl): self
    {
        $this->bookmarkUrl = $bookmarkUrl;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setBookmark($this);
        }

        return $this;
    }

    /**
     * @param Tag[] $tags
     */
    public function addTags(array $tags): self
    {
        foreach ($tags as $tag) {
            if (! $tag instanceof Tag) {
                throw new \LogicException(sprintf(
                    '%s accepts an array of %s instance, given a wrong value',
                    __METHOD__,
                    Tag::class
                ));
            }

            $this->addTag($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            if ($tag->getBookmark() === $this) {
                $tag->setBookmark(null);
            }
        }

        return $this;
    }

    public function hasTag(Tag $tag): bool
    {
       return $this->tags->contains($tag);
    }
}

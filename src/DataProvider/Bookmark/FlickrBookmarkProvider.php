<?php

namespace App\DataProvider\Bookmark;

use App\Entity\Bookmark;
use App\Entity\Tag;
use Embed\Embed;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FlickrBookmarkProvider implements BookmarkProviderInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createTypedBookmark(Bookmark $bookmark, array $options = []): Bookmark
    {
        try {
            if (!empty($options['data'])) {
                $bookmark = $this->serializer->deserialize(
                    $options['data'],
                    Bookmark\BookmarkImage::class,
                    'json',
                    ['write', 'create']
                );
            } else {
                $bookmark->setType(Bookmark::BOOKMARK_IMAGE);
            }
        } catch (ExceptionInterface $exception) {
            throw new \InvalidArgumentException('Invalid data provided', Response::HTTP_BAD_REQUEST, $exception);
        }

        return $this->embedBookmark($bookmark);
    }

    public function support(Bookmark $bookmark): bool
    {
        return preg_match('/^(http|https):\/\/(www.)?flickr.com/', $bookmark->getBookmarkUrl());
    }

    private function embedBookmark(Bookmark\BookmarkImage $bookmark): Bookmark\BookmarkImage
    {
        $embed = new Embed();
        $document = $embed->get($bookmark->getBookmarkUrl());

        $bookmark
            ->setAuthor($document->authorName)
            ->setTitle($document->title)
            ->setUrl($document->url)
            ->setHeight($document->code->height)
            ->setWidth($document->code->width);

        foreach ($document->keywords as $tag) {
            $bookmark->addTag(Tag::createFromName($tag));
        }

        return $bookmark;
    }
}
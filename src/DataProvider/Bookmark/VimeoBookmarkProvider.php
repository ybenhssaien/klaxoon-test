<?php

namespace App\DataProvider\Bookmark;

use App\Entity\Bookmark;
use App\Entity\Tag;
use Embed\Embed;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class VimeoBookmarkProvider implements BookmarkProviderInterface
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
                    Bookmark\BookmarkVideo::class,
                    'json',
                    ['write', 'create']
                );
            } else {
                $bookmark->setType(Bookmark::BOOKMARK_VIDEO);
            }
        } catch (ExceptionInterface $exception) {
            throw new \InvalidArgumentException('Invalid data provided', Response::HTTP_BAD_REQUEST, $exception);
        }

        return $this->embedBookmark($bookmark);
    }

    public function support(Bookmark $bookmark): bool
    {
        return preg_match('/^(http|https):\/\/(www.)?vimeo.com/', $bookmark->getBookmarkUrl());
    }

    private function embedBookmark(Bookmark\BookmarkVideo $bookmark): Bookmark\BookmarkVideo
    {
        $embed = new Embed();
        $document = $embed->get($bookmark->getBookmarkUrl());

        $bookmark
            ->setAuthor($document->authorName)
            ->setTitle($document->title)
            ->setUrl($document->url);

        if ($document->image && list($width, $height) = getimagesize($document->image)) {
            $bookmark
                ->setHeight($height)
                ->setWidth($width)
                ->setHeight($document->code->height)
                ->setWidth($document->code->width)
                ->setDuration($document->getOEmbed()->get('duration'));
        }

        foreach ($document->keywords as $tag) {
            $bookmark->addTag(Tag::createFromName($tag));
        }

        return $bookmark;
    }
}
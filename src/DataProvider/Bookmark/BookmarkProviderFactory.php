<?php

namespace App\DataProvider\Bookmark;

use App\Entity\Bookmark;

class BookmarkProviderFactory
{
    protected array $bookmarkProviders = [];

    public function __construct(
        FlickrBookmarkProvider $flickrBookmarkProvider,
        VimeoBookmarkProvider $vimeoBookmarkProvider
    ) {
        $this->bookmarkProviders = [
            $flickrBookmarkProvider,
            $vimeoBookmarkProvider,
        ];
    }

    /**
     * @return BookmarkProviderInterface[]
     */
    public function getBookmarkProviders(): array
    {
        return $this->bookmarkProviders;
    }

    public function getBookmarkProvider(Bookmark $bookmark): BookmarkProviderInterface
    {
        foreach ($this->bookmarkProviders as $bookmarkProvider) {
            if ($bookmarkProvider->support($bookmark)) {
                return $bookmarkProvider;
            }
        }

        throw new \LogicException(sprintf('No provider can handle the boomark url "%s"', $bookmark->getBookmarkUrl()));
    }

    public function createTypedBookmark(Bookmark $bookmark, array $options = []): Bookmark
    {
        return $this->getBookmarkProvider($bookmark)->createTypedBookmark($bookmark, $options);
    }
}
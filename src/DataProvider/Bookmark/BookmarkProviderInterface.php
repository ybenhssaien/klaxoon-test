<?php

namespace App\DataProvider\Bookmark;

use App\Entity\Bookmark;

interface BookmarkProviderInterface
{
    public function createTypedBookmark(Bookmark $bookmark, array $options = []): Bookmark;

    public function support(Bookmark $bookmark): bool;
}
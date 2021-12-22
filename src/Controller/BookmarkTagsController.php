<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Entity\Tag;
use App\Manager\BookmarkManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route(
 *     path="/api/bookmarks/{bookmark}/tags",
 *     requirements={
 *      "bookmark"=AbstractApiController::UUID_REGEX
 *     },
 * )
 */
class BookmarkTagsController extends AbstractApiController
{
    /**
     * @Route(name="api_bookmarks_bookmark_tags_get", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/bookmarks/{bookmark}/tags",
     *     tags={"tags"},
     *     @OA\Response(
     *      response="200",
     *      description="All bookmark tags list",
     *      @OA\JsonContent(ref="#/components/schemas/TagRead"),
     *    )
     * )
     */
    public function getBookmarkTags(Bookmark $bookmark): Response
    {
        return $this->jsonSerialized($bookmark->getTags());
    }

    /**
     * @Route(
     *     path="/{tag}",
     *     name="api_bookmarks_bookmark_tag_get",
     *     requirements={
     *      "tag"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"GET"}
     * )
     *
     * @OA\Get(
     *     path="/api/bookmarks/{bookmark}/tags/{tag}",
     *     tags={"tags"},
     *     @OA\Response(
     *      response="200",
     *      description="Bookmark tag details",
     *      @OA\JsonContent(ref="#/components/schemas/TagRead"),
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Tag doesn't belong to the bookmark",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat")
     *    ),
     * )
     */
    public function getBookmarkTag(Bookmark $bookmark, Tag $tag): Response
    {
        $this->assertTag($tag, $bookmark);

        return $this->jsonSerialized($tag);
    }

    /**
     * @Route(
     *     name="api_bookmarks_bookmark_tags_create",
     *     methods={"POST"},
     *     condition="request.headers.get('Content-Type') === 'application/json'"
     * )
     *
     * @OA\Post(
     *     path="/api/bookmarks/{bookmark}/tags",
     *     tags={"tags"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/TagUpdate")),
     *     @OA\Response(
     *      response="200",
     *      description="All bookmark tags list with the created tag",
     *      @OA\JsonContent(@OA\Items(ref="#/components/schemas/TagRead"))
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Invalid user data provided",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorsFormat"),
     *    ),
     *    @OA\Response(
     *          response="500",
     *          description="Tag cannot be created for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function addBookmarkTag(
        Bookmark $bookmark,
        BookmarkManager $bookmarkManager
    ): Response {
        /** @var Tag[] $tags */
        $tags = $this->getRequestContentDeserialized(Tag::class.'[]', ['write', 'create']);

        if ($tags instanceof Response) {
            return $tags;
        }

        if (! $bookmarkManager->persistBookmark($bookmark->addTags($tags))) {
            throw new \RuntimeException(sprintf('Cannot create tags for bookrmark "%s"', $bookmark->getId()));
        }

        return $this->jsonSerialized($bookmark->getTags());
    }

    /**
     * @Route(
     *     path="/{tag}",
     *     name="api_bookmarks_bookmark_tag_update",
     *     requirements={
     *      "tag"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"PUT"},
     *     condition="request.headers.get('Content-Type') === 'application/json'"
     * )
     *
     * @OA\Put(
     *     path="/api/bookmarks/{bookmark}/tags/{tag}",
     *     tags={"tags"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/TagUpdate")),
     *     @OA\Response(
     *      response="200",
     *      description="Updated bookmark details",
     *      @OA\JsonContent(ref="#/components/schemas/TagRead"),
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Invalid user data provided or tag doesn't belong to the bookmark",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorsFormat"),
     *    ),
     *    @OA\Response(
     *          response="500",
     *          description="Tag cannot cannot be updated for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function updateBookmarkTag(Bookmark $bookmark, Tag $tag, BookmarkManager $bookmarkManager): Response
    {
        $this->assertTag($tag, $bookmark);

        /** @var Tag $tag */
        $tag = $this->getRequestContentDeserialized($tag, ['write', 'edit']);

        if ($tag instanceof Response) {
            return $tag;
        }

        if (! $bookmarkManager->persistBookmark($bookmark)) {
            throw new \RuntimeException(sprintf('Cannot update tag "%s"', $tag->getId()));
        }

        return $this->jsonSerialized($tag);
    }

    /**
     * @Route(
     *     path="/{tag}",
     *     name="api_bookmarks_bookmark_tag_delete",
     *     requirements={
     *      "tag"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"DELETE"}
     * )
     *
     * @OA\Delete(
     *    path="/api/bookmarks/{bookmark}/tags/{tag}",
     *    tags={"tags"},
     *    @OA\Response(response="204", description="Tag deleted"),
     *    @OA\Response(
     *          response="400",
     *          description="Tag doesn't belong to the bookmark",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat")
     *    ),
     *    @OA\Response(
     *          response="500",
     *          description="Tag cannot be deleted for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function deleteBookmarkTag(Bookmark $bookmark, Tag $tag, BookmarkManager $bookmarkManager): Response
    {
        $this->assertTag($tag, $bookmark);

        if (! $bookmarkManager->persistBookmark($bookmark->removeTag($tag))) {
            throw new \RuntimeException(sprintf('Cannot remove tag "%s"', $tag->getId()));
        }

        return $this->noContent();
    }

    /**
     * Assert Tag belong to a bookmark
     */
    private function assertTag(Tag $tag, Bookmark $bookmark): void
    {
        if (! $bookmark->hasTag($tag)) {
            throw new \InvalidArgumentException(
                sprintf('The tag "%s" doesn\'t belong to this bookmark', $tag),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
<?php

namespace App\Controller;

use App\DataProvider\Bookmark\BookmarkProviderFactory;
use App\Entity\Bookmark;
use App\Manager\BookmarkManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route(path="/api/bookmarks")
 *
 * @OA\Info(
 *     title="Bookmarks API resources",
 *     version="0.1",
 *     @OA\Contact(email="youssef.benhssaien@gmail.com")
 * )
 */
class BookmarkController extends AbstractApiController
{
    /**
     * @Route(name="api_bookmarks_get", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/bookmarks",
     *     tags={"bookmarks"},
     *     @OA\Response(
     *      response="200",
     *      description="All bookmarks list",
     *      @OA\JsonContent(ref="#/components/schemas/BookmarkRead"),
     *    )
     * )
     */
    public function allBookmarks(BookmarkManager $bookmarkManager): Response
    {
        return $this->jsonSerialized($bookmarkManager->getAllBookmarks());
    }

    /**
     * @Route(
     *     path="/{bookmark}",
     *     name="api_bookmarks_bookmark_get",
     *     requirements={
     *      "bookmark"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"GET"}
     * )
     *
     * @OA\Get(
     *     path="/api/bookmarks/{bookmark}",
     *     tags={"bookmarks"},
     *     @OA\Response(
     *      response="200",
     *      description="Bookmark details",
     *      @OA\JsonContent(ref="#/components/schemas/BookmarkRead"),
     *    )
     * )
     */
    public function getBookmark(Bookmark $bookmark): Response
    {
        return $this->jsonSerialized($bookmark);
    }

    /**
     * @Route(
     *     name="api_bookmarks_bookmark_create",
     *     methods={"POST"},
     *     condition="request.headers.get('Content-Type') === 'application/json'"
     * )
     *
     * @OA\Post(
     *     path="/api/bookmarks",
     *     tags={"bookmarks"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/BookmarkCreate")),
     *     @OA\Response(
     *      response="200",
     *      description="Created bookmark details",
     *      @OA\JsonContent(ref="#/components/schemas/BookmarkRead")
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Invalid user data provided",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorsFormat"),
     *    ),
     *    @OA\Response(
     *          response="500",
     *          description="Bookmark cannot be created for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function addBookmark(
        BookmarkManager $bookmarkManager,
        BookmarkProviderFactory $bookmarkProviderFactory,
        Request $request
    ): Response {
        /** @var Bookmark $bookmark */
        $bookmark = $this->getRequestContentDeserialized(Bookmark::class, ['write', 'create']);

        if ($bookmark instanceof Response) {
            return $bookmark;
        }

        $bookmark = $bookmarkProviderFactory->createTypedBookmark($bookmark, [
            'data' => $request->getContent(),
        ]);

        if (! $bookmarkManager->persistBookmark($bookmark)) {
            throw new \RuntimeException('Cannot create bookmark');
        }

        return $this->jsonSerialized($bookmark);
    }

    /**
     * @Route(
     *     path="/{bookmark}",
     *     name="api_bookmarks_bookmark_update",
     *     requirements={
     *      "bookmark"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"PUT"},
     *     condition="request.headers.get('Content-Type') === 'application/json'"
     * )
     *
     * @OA\Put(
     *     path="/api/bookmarks/{bookmark}",
     *     tags={"bookmarks"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/BookmarkUpdate")),
     *     @OA\Response(
     *      response="200",
     *      description="Updated bookmark details",
     *      @OA\JsonContent(ref="#/components/schemas/BookmarkRead"),
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Invalid user data provided",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorsFormat"),
     *    ),
     *    @OA\Response(
     *          response="500",
     *          description="Bookmark cannot be updated for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function updateBookmark(Bookmark $bookmark, BookmarkManager $bookmarkManager): Response
    {
        $bookmark = $this->getRequestContentDeserialized($bookmark, ['write', 'edit']);

        if ($bookmark instanceof Response) {
            return $bookmark;
        }

        if (! $bookmarkManager->persistBookmark($bookmark)) {
            throw new \RuntimeException(sprintf('Cannot update bookmark "%s"', $bookmark->getId()));
        }

        return $this->jsonSerialized($bookmark);
    }

    /**
     * @Route(
     *     path="/{bookmark}",
     *     name="api_bookmarks_bookmark_delete",
     *     requirements={
     *      "bookmark"=AbstractApiController::UUID_REGEX
     *     },
     *     methods={"DELETE"}
     * )
     *
     * @OA\Delete(
     *    path="/api/bookmarks/{bookmark}",
     *    tags={"bookmarks"},
     *    @OA\Response(response="204", description="Bookmark deleted"),
     *    @OA\Response(
     *          response="500",
     *          description="Bookmark cannot be deleted for an unkown reason",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorFormat"),
     *    ),
     * )
     */
    public function deleteBookmark(BookmarkManager $bookmarkManager, Bookmark $bookmark): Response
    {
        if (! $bookmarkManager->removeBookmark($bookmark)) {
            throw new \RuntimeException(sprintf('Cannot remove bookmark "%s"', $bookmark->getId()));
        }

        return $this->noContent();
    }
}
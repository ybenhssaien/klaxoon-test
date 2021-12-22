<?php

namespace App\Manager;

use App\Entity\Bookmark;
use App\Entity\Tag;
use App\Repository\BookmarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class BookmarkManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected EntityManagerInterface $entityManager;
    protected BookmarkRepository $bookmarkRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->bookmarkRepository = $entityManager->getRepository(Bookmark::class);
    }

    public function persistBookmark(Bookmark $bookmark): bool
    {
        try {
            $this->entityManager->persist($bookmark);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());

            return false;
        }
    }

    public function removeBookmark(Bookmark $bookmark): bool
    {
        try {
            $this->entityManager->remove($bookmark);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getAllBookmarks(): array
    {
        return $this->bookmarkRepository->findAll();
    }

    public function getOrCreateTagByName(string $name): Tag
    {
    }
}
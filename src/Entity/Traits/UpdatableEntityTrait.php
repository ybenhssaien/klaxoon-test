<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="UpdatableResource")
 */
trait UpdatableEntityTrait
{
    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"read"})
     *
     * @OA\Property(
     *     type="string",
     *     description="The create date"
     * )
     */
    protected \DateTimeInterface $createDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"read"})
     *
     * @OA\Property(
     *     type="string",
     *     description="The update date"
     * )
     */
    protected ?\DateTimeInterface $updateDate = null;

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createDate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updateDate = new \DateTime();
    }
}
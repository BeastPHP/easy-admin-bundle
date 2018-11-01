<?php

namespace Beast\EasyAdminBundle\Entity\Core\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait SoftDeleteableEntity
 *
 * @package Beast\EasyAdminBundle\Entity\Core\Traits
 */
trait SoftDeleteableEntity
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Sets deletedAt.
     *
     * @param \DateTime|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt = null): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Returns deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}

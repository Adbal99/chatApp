<?php

namespace App\Entity;

/**
 * chat read timestamps
 */
trait Timestamp
{
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    

    /**
     * @return Datetime
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \Datetime();
    }
}

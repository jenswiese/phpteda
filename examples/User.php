<?php

/**
 * Example User
 *
 * @author Jens Wiese <jens@howtrueisfalse.de>
 * @since 2013-03-09
 */
class User
{
    /** @var string */
    protected $firstname;

    /** @var string */
    protected $lastname;

    /** @var string */
    protected $email;

    /** @var DateTime */
    protected $createdAt;

    /** @var bool */
    protected $isActive;

    /** @var bool */
    protected $isDeleted;

    /** @var bool */
    protected $isBlocked;

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isBlocked
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;
    }

    /**
     * @return boolean
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

    /**
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $attributes = array(
            $this->getFirstname(),
            $this->getLastname(),
            $this->getEmail(),
            $this->getCreatedAt()->format('d.m.Y H:i:s'),
            $this->getIsActive(),
            $this->getIsDeleted(),
            $this->getIsBlocked()
        );

        return implode(';', $attributes) . "\n";
    }
}

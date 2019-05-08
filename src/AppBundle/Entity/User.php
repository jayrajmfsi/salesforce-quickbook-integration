<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements AdvancedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=30)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="qb_realmId", type="string", length=20, nullable=true)
     */
    private $QBrealmId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean"
     *  , options={"comment":"0 means user in-active, 1 means user active", "default":"1"})
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="sf_accountId", type="string", length=30, nullable=true)
     */
    private $SFaccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="sf_instance_url", type="string", length=100)
     */
    private $SFinstanceUrl;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct()
    {
        $this->roles = [];
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @param $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set qBrealmId
     *
     * @param string $qBrealmId
     *
     * @return User
     */
    public function setQBrealmId($qBrealmId)
    {
        $this->QBrealmId = $qBrealmId;

        return $this;
    }

    /**
     * Get qBrealmId
     *
     * @return string
     */
    public function getQBrealmId()
    {
        return $this->QBrealmId;
    }

    /**
     * Set sFaccountId
     *
     * @param string $sFaccountId
     *
     * @return User
     */
    public function setSFaccountId($sFaccountId)
    {
        $this->SFaccountId = $sFaccountId;

        return $this;
    }

    /**
     * Get sFaccountId
     *
     * @return string
     */
    public function getSFaccountId()
    {
        return $this->SFaccountId;
    }

    /**
     * Set sFinstanceUrl
     *
     * @param string $sFinstanceUrl
     *
     * @return User
     */
    public function setSFinstanceUrl($sFinstanceUrl)
    {
        $this->SFinstanceUrl = $sFinstanceUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
    /**
     * Get sFinstanceUrl
     *
     * @return string
     */
    public function getSFinstanceUrl()
    {
        return $this->SFinstanceUrl;
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * String representation of the user object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }
}

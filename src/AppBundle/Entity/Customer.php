<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="Customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Customer
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
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="qb_cust_id", type="string", length=10, nullable=true)
     */
    private $qbCustId;

    /**
     * @var string
     *
     * @ORM\Column(name="sf_cust_id", type="string", length=30, nullable=true)
     */
    private $sfCustId;

    /**
     * @var string
     *
     * @ORM\Column(name="qb_sync_token", type="string", length=10, nullable=true)
     */
    private $qbSyncToken;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=15, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="mailingStreet", type="string", length=50, nullable=true)
     */
    private $mailingStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="mailingCity", type="string", length=20, nullable=true)
     */
    private $mailingCity;

    /**
     * @var string
     *
     * @ORM\Column(name="mailingState", type="string", length=50, nullable=true)
     */
    private $mailingState;

    /**
     * @var string
     *
     * @ORM\Column(name="mailingPostalCode", type="string", length=10, nullable=true)
     */
    private $mailingPostalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="mailingCountry", type="string", length=50, nullable=true)
     */
    private $mailingCountry;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

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
     * Set qbCustId
     *
     * @param string $qbCustId
     *
     * @return Customer
     */
    public function setQbCustId($qbCustId)
    {
        $this->qbCustId = $qbCustId;

        return $this;
    }

    /**
     * Get qbCustId
     *
     * @return string
     */
    public function getQbCustId()
    {
        return $this->qbCustId;
    }

    /**
     * Set sfCustId
     *
     * @param string $sfCustId
     *
     * @return Customer
     */
    public function setSfCustId($sfCustId)
    {
        $this->sfCustId = $sfCustId;

        return $this;
    }

    /**
     * Get sfCustId
     *
     * @return string
     */
    public function getSfCustId()
    {
        return $this->sfCustId;
    }

    /**
     * Set qbSyncToken
     *
     * @param string $qbSyncToken
     *
     * @return Customer
     */
    public function setQbSyncToken($qbSyncToken)
    {
        $this->qbSyncToken = $qbSyncToken;

        return $this;
    }

    /**
     * Get qbSyncToken
     *
     * @return string
     */
    public function getQbSyncToken()
    {
        return $this->qbSyncToken;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Customer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mailingStreet
     *
     * @param string $mailingStreet
     *
     * @return Customer
     */
    public function setMailingStreet($mailingStreet)
    {
        $this->mailingStreet = $mailingStreet;

        return $this;
    }

    /**
     * Get mailingStreet
     *
     * @return string
     */
    public function getMailingStreet()
    {
        return $this->mailingStreet;
    }

    /**
     * Set mailingCity
     *
     * @param string $mailingCity
     *
     * @return Customer
     */
    public function setMailingCity($mailingCity)
    {
        $this->mailingCity = $mailingCity;

        return $this;
    }

    /**
     * Get mailingCity
     *
     * @return string
     */
    public function getMailingCity()
    {
        return $this->mailingCity;
    }

    /**
     * Set mailingState
     *
     * @param string $mailingState
     *
     * @return Customer
     */
    public function setMailingState($mailingState)
    {
        $this->mailingState = $mailingState;

        return $this;
    }

    /**
     * Get mailingState
     *
     * @return string
     */
    public function getMailingState()
    {
        return $this->mailingState;
    }

    /**
     * Set mailingPostalCode
     *
     * @param string $mailingPostalCode
     *
     * @return Customer
     */
    public function setMailingPostalCode($mailingPostalCode)
    {
        $this->mailingPostalCode = $mailingPostalCode;

        return $this;
    }

    /**
     * Get mailingPostalCode
     *
     * @return string
     */
    public function getMailingPostalCode()
    {
        return $this->mailingPostalCode;
    }

    /**
     * Set mailingCountry
     *
     * @param string $mailingCountry
     *
     * @return Customer
     */
    public function setMailingCountry($mailingCountry)
    {
        $this->mailingCountry = $mailingCountry;

        return $this;
    }

    /**
     * Get mailingCountry
     *
     * @return string
     */
    public function getMailingCountry()
    {
        return $this->mailingCountry;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return Customer
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }
}

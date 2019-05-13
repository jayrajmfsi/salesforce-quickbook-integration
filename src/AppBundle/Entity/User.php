<?php
/**
 *  User Entity
 *  @category Entity
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="qb_realm_id", type="string", length=20, nullable=true)
     */
    private $qbRealmId;

    /**
     * @var string
     *
     * @ORM\Column(name="unique_id", type="string", nullable=true)
     */
    private $uniqueId;

    /**
     * @var string
     *
     * @ORM\Column(name="sf_account_id", type="string", length=30, nullable=true)
     */
    private $sfAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="sf_instance_url", type="string", length=100, nullable=true)
     */
    private $sfInstanceUrl;

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
     * @return string
     */
    public function getQbRealmId()
    {
        return $this->qbRealmId;
    }

    /**
     * @param string $qbRealmId
     */
    public function setQbRealmId($qbRealmId)
    {
        $this->qbRealmId = $qbRealmId;
    }

    /**
     * @return string
     */
    public function getSfAccountId()
    {
        return $this->sfAccountId;
    }

    /**
     * @param string $sfAccountId
     */
    public function setSfAccountId($sfAccountId)
    {
        $this->sfAccountId = $sfAccountId;
    }

    /**
     * @return string
     */
    public function getSfInstanceUrl()
    {
        return $this->sfInstanceUrl;
    }

    /**
     * @param string $sfInstanceUrl
     */
    public function setSfInstanceUrl($sfInstanceUrl)
    {
        $this->sfInstanceUrl = $sfInstanceUrl;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param $uniqueId
     * @return $this
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->uniqueId = uniqid();
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}

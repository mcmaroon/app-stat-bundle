<?php

namespace App\StatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stat
 *
 * @ORM\Entity
 * @ORM\Table(name="stat")
 * @ORM\Entity(repositoryClass="App\StatBundle\Entity\StatRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Stat {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var
     * @ORM\Column(name="type_group", type="string", length=30, nullable=true)
     */
    private $typeGroup;

    /**
     * @var
     * @ORM\Column(name="type_group_key", type="integer", nullable=true)
     */
    private $typeGroupKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Stat
     */
    public function setType($type) {
        $this->type = (string) trim($type);

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set typeGroup
     *
     * @param string $typeGroup
     *
     * @return Stat
     */
    public function setTypeGroup($typeGroup) {
        $this->typeGroup = (string) trim($typeGroup);

        return $this;
    }

    /**
     * Get typeGroup
     *
     * @return string
     */
    public function getTypeGroup() {
        return $this->typeGroup;
    }

    /**
     * Set typeGroupKey
     *
     * @param integer $typeGroupKey
     *
     * @return Stat
     */
    public function setTypeGroupKey($typeGroupKey) {
        $this->typeGroupKey = (int) $typeGroupKey;

        return $this;
    }

    /**
     * Get typeGroupKey
     *
     * @return integer
     */
    public function getTypeGroupKey() {
        return $this->typeGroupKey;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Stat
     */
    public function setValue($value) {
        $this->value = (int) $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @ORM\PrePersist()
     * @return Stat
     */
    public function setCreatedAt($date = null) {

        if (!is_null($date) && $date instanceof \DateTime) {
            $this->createdAt = $date;
        }

        if (is_null($this->createdAt)) {
            $this->createdAt = new \DateTime("now");
        }

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function __toString() {
        return (string) $this->getId() . $this->getType();
    }

}

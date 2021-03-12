<?php

namespace ERTNoteMaker\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use ERTNoteMaker\Model;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="notes")
 */
class Note extends Model {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $uuid;

    /**
     * @ORM\OneToOne(targetEntity="RaidEncounter")
     *
     * @var RaidEncounter
     */
    protected $raidEncounter;

    // protected $cooldowns;

    /**
     * Constructor
     *
     * @param RaidEncounter $encounter
     */
    public function __construct() {
        $this->setUuid(Uuid::uuid4());
    }

    /**
     * Get the value of raidEncounter
     *
     * @return  RaidEncounter
     */
    public function getRaidEncounter() : RaidEncounter {
        return $this->raidEncounter;
    }

    /**
     * Set the value of raidEncounter
     *
     * @param   RaidEncounter  $raidEncounter
     *
     * @return  self
     */
    public function setRaidEncounter(RaidEncounter $raidEncounter) : self {
        $this->raidEncounter = $raidEncounter;
        return $this;
    }

    /**
     * Get the value of uuid
     *
     * @return  string
     */
    public function getUuid() : string {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @param   string  $uuid
     *
     * @return  self
     */
    public function setUuid(string $uuid) : self {
        $this->uuid = $uuid;
        return $this;
    }
}

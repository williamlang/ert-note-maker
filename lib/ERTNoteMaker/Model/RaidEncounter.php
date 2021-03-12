<?php

namespace ERTNoteMaker\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping as ORM;
use ERTNoteMaker\Model;
use ERTNoteMaker\Model\Raid;

/**
 * @ORM\Entity
 * @ORM\Table(name="raid_encounters")
 */
class RaidEncounter extends Model {

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
    protected $name;

    /**
     * @ORM\Column(type="integer", unique=true)
     *
     * @var int
     */
    protected $encounterId;

    /**
     * @ORM\ManyToOne(targetEntity="Raid", inversedBy="encounters")
     * @var Raid
     */
    protected $raid;

    /**
     * @ORM\OneToMany(targetEntity="RaidEncounterAbility", mappedBy="raid_encounter", indexBy="raid_encounter_ability_id")
     * @var PersistentCollection
     */
    protected $abilities;

    public function __construct(Raid $raid, string $name, int $encounterId) {
        $this->setRaid($raid);
        $this->setName($name);
        $this->setEncounterId($encounterId);
        $this->abilities = new ArrayCollection();
    }

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param   string  $name
     *
     * @return  self
     */
    public function setName(string $name) : self {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of encounterId
     *
     * @return  int
     */
    public function getEncounterId() : int {
        return $this->encounterId;
    }

    /**
     * Set the value of encounterId
     *
     * @param   int  $encounterId
     *
     * @return  self
     */
    public function setEncounterId($encounterId) : self {
        $this->encounterId = $encounterId;
        return $this;
    }

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * Add an encounter to this raid
     *
     * @param RaidEncounter $encounter
     * @return self
     */
    public function addEncounter(RaidEncounter $encounter) : self {
        $this->encounters[$encounter->getId()] = $encounter;
        return $this;
    }

    /**
     * Get all the encounters in the raid
     *
     * @return ArrayCollection
     */
    public function getEncounters() : array {
        return $this->encounters->toArray();
    }

    /**
     * Get the value of raid
     *
     * @return  Raid
     */
    public function getRaid() : Raid {
        return $this->raid;
    }

    /**
     * Set the value of raid
     *
     * @param   Raid  $raid
     *
     * @return  self
     */
    public function setRaid(Raid $raid) : self {
        $this->raid = $raid;
        return $this;
    }

    /**
     * Get the value of abilities
     *
     * @return  PersistentCollection
     */
    public function getAbilities() : PersistentCollection {
        return $this->abilities;
    }
}

<?php

namespace ERTNoteMaker\Model;

use Doctrine\ORM\Mapping as ORM;
use ERTNoteMaker\Model;
use ERTNoteMaker\Model\RaidEncounter;

/**
 * @ORM\Entity
 * @ORM\Table(name="raid_encounter_abilities")
 */
class RaidEncounterAbility extends Model {
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
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $spellId;

    /**
     * @ORM\ManyToOne(targetEntity="RaidEncounter", inversedBy="abilities")
     * @var RaidEncounter
     */
    protected $raidEncounter;

    /**
     * Constructor
     *
     * @param RaidEncounter $encounter
     * @param string $name
     * @param integer $spellId
     */
    public function __construct(RaidEncounter $encounter, string $name, int $spellId) {
        $this->setRaidEncounter($encounter);
        $this->setName($name);
        $this->setSpellId($spellId);
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
     * Get the value of spellId
     *
     * @return  int
     */
    public function getSpellId() : int {
        return $this->spellId;
    }

    /**
     * Set the value of spellId
     *
     * @param   int  $spellId
     *
     * @return  self
     */
    public function setSpellId(int $spellId) : self {
        $this->spellId = $spellId;
        return $this;
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
}

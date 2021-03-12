<?php

namespace ERTNoteMaker\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use ERTNoteMaker\Model;

/**
 * @ORM\Entity
 * @ORM\Table(name="raids")
 */
class Raid extends Model {

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
     * @ORM\OneToMany(targetEntity="RaidEncounter", mappedBy="raid", indexBy="raid_encounter_id")
     * @var PersistentCollection
     */
    private $encounters;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name) {
        $this->setName($name);
        $this->encounters = new ArrayCollection();
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
     * Get the value of encounters
     *
     * @return  ArrayCollection
     */
    public function getEncounters() : PersistentCollection {
        return $this->encounters;
    }
}

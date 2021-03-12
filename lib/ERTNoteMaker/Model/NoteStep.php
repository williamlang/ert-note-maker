<?php

namespace ERTNoteMaker\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use ERTNoteMaker\Model;

/**
 * @ORM\Entity
 * @ORM\Table(name="note_steps")
 */
class NoteStep extends Model {

    const TYPE_PHASE = "phase";
    const TYPE_COOLDOWN = "cooldown";
    const TRIGGERED_BY_ABILITY = "boss_ability";
    const TRIGGERED_BY_HEALTH = "boos_health";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('phase', 'cooldown')"))
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('boss_ability', 'boss_health')")
     *
     * @var string
     */
    protected $triggeredBy;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $triggeredByValue;

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * Get the value of type
     *
     * @return  string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param   string  $type
     *
     * @return  self
     */
    public function setType(string $type) : self {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of triggeredByValue
     *
     * @return  int
     */
    public function getTriggeredByValue() : int {
        return $this->triggeredByValue;
    }

    /**
     * Set the value of triggeredByValue
     *
     * @param   int  $triggeredByValue
     *
     * @return  self
     */
    public function setTriggeredByValue(int $triggeredByValue) : self {
        $this->triggeredByValue = $triggeredByValue;
        return $this;
    }

    /**
     * Get the value of triggeredBy
     *
     * @return  string
     */
    public function getTriggeredBy() : string {
        return $this->triggeredBy;
    }

    /**
     * Set the value of triggeredBy
     *
     * @param   string  $triggeredBy
     *
     * @return  self
     */
    public function setTriggeredBy(string $triggeredBy) : self {
        $this->triggeredBy = $triggeredBy;
        return $this;
    }
}

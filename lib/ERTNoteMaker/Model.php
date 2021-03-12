<?php

namespace ERTNoteMaker;

use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use ReflectionMethod;
use ERTNoteMaker\Database;

/**
 * The Base Model class all other models extend off of
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Model {

    /**
     * @ORM\Column(name="created_at",type="datetime",nullable=false)
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at",type="datetime",nullable=false)
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * Get the created at time
     *
     * @return DateTime
     */
    public function getCreatedAt() : Carbon {
        return !is_null($this->createdAt) ? Carbon::instance($this->createdAt) : Carbon::now('UTC');
    }

    /**
     * Get the update at time
     *
     * @return DateTime
     */
    public function getUpdatedAt() : Carbon {
        return !is_null($this->updatedAt) ? Carbon::instance($this->updatedAt) : Carbon::now('UTC');
    }

    /**
     * Set the creation time
     *
     * @param DateTime $dt
     * @return void
     */
    public function setCreatedAt(DateTime $dt) {
        $this->createdAt = $dt;
    }

    /**
     * Set the updated time
     *
     * @param DateTime $dt
     * @return void
     */
    public function setUpdatedAt(DateTime $dt) {
        $this->updatedAt = $dt;
    }

    /**
     * @ORM\PrePersist
     *
     * @param Model $model
     * @return void
     */
    public function prePersist() {
        $this->setUpdatedAt(Carbon::now('UTC'));
        $this->setCreatedAt(Carbon::now('UTC'));
    }

    /**
     * @ORM\PreUpdate
     *
     * @param Model $model
     * @return void
     */
    public function preUpdate() {
        $this->setUpdatedAt(Carbon::now('UTC'));
    }

    /**
     * Save the model
     *
     * @return void
     */
    public function save(bool $flush = true) {
        $entityManager = Database::$instance->getEntityManager();
        $entityManager->persist($this);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function delete(bool $flush = true) {
        $entityManager = Database::$instance->getEntityManager();
        $entityManager->remove($this);

        if ($flush) {
            $entityManager->flush();
        }
    }

    /**
     * Model to array
     *
     * @return array
     */
    public function toArray(int $maxDepth = 0, int $depth = 0) : array {
        $rc = new ReflectionClass($this);
        $methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);

        $vars = [
            'createdAt' => Carbon::instance($this->getCreatedAt())->toDateTimeString(),
            'updatedAt' => Carbon::instance($this->getCreatedAt())->toDateTimeString()
        ];
        foreach ($methods as $method) {
            if (preg_match('/^get/', $method->name) === 1 && !$method->isStatic()) {
                $propertyName  = lcfirst(str_replace('get', '', $method->name));

                $returnType = $method->getReturnType();
                if (empty($method->getParameters())) {
                    if (!empty($returnType) && $returnType->isBuiltin()) {
                        $propertyValue = $method->invoke($this);

                        if (!is_array($propertyValue)) {
                            $vars[$propertyName] = $propertyValue;
                        }
                    } else {
                        if ($depth < $maxDepth) {
                            $propertyValue = $method->invoke($this);

                            if (!empty($propertyValue) && $propertyValue instanceof \Tycoon\Game\Model) {
                                $vars[$propertyName] = $propertyValue->toArray($maxDepth, $depth + 1);
                            } else if (!empty($propertyValue) && $propertyValue instanceof \DateTime) {
                                $vars[$propertyName] = Carbon::instance($propertyValue)->toDateString();
                            }
                        }
                    }
                }
            }
        }

        return $vars;
    }
}

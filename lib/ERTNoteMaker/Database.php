<?php

namespace ERTNoteMaker;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Yaml\Yaml;

class Database {

    /**
     * Singleton Object
     *
     * @var Database
     */
    public static $instance;

    /**
     * Doctrine EntityManager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Repositories
     *
     * @var EntityRepository[]
     */
    protected static $repositories;

    /**
     * Private Constructor
     */
    private function __construct($isDevMode = true) {
        $config = Setup::createAnnotationMetadataConfiguration(array(BASEDIR . "/lib/ERTNoteMaker/Model/"), $isDevMode, null, null, false);

        // set the naming strategy
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        // database configuration parameters
        $db = Yaml::parseFile(BASEDIR . '/config/config.database.yml');

        // obtaining the entity manager
        $this->entityManager = EntityManager::create($db, $config);

        // logging queryies
        $logger = new \Doctrine\DBAL\Logging\DebugStack();
        $this->entityManager->getConfiguration()->setSQLLogger($logger);
    }

    /**
     * Create the Database Connection
     *
     * @return void
     */
    public static function create($isDevMode = true) : Database {
        if (empty(self::$instance)) {
            self::$instance = new Database($isDevMode);
        }

        return self::$instance;
    }

    /**
     * Get the EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager() : EntityManager {
        return $this->entityManager;
    }

    /**
     * Get a repository for a model
     *
     * @param string $class
     * @return EntityRepository
     */
    public function getRepository(string $class) : EntityRepository {
        if (empty(self::$repositories[$class])) {
            self::$repositories[$class] = $this->getEntityManager()->getRepository($class);
        }

        return self::$repositories[$class];
    }

    // /**
    //  * Get the QueryBuilder to run complex queries
    //  *
    //  * @return QueryBuilder
    //  */
    // public function getQueryBuilder() : QueryBuilder {
    //     return $this->getEntityManager()->getQueryBuilder();
    // }
}
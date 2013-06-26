<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\MysqlBundle\Adapter\AdapterInterface;
use Jeboehm\Lampcp\MysqlBundle\Exception\EmptyDatabasePrefixException;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;
use Jeboehm\Lampcp\MysqlBundle\Transformer\DatabaseModelTransformer;

/**
 * Class SyncService
 *
 * One way synchronisation:
 * LampCP -> Database
 *
 * - Add/update/delete databases
 * - Add/update/delete users
 * - Add/update/delete permissions
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class SyncService
{
    /** @var AdapterInterface */
    private $adapter;
    /** @var ConfigService */
    private $config_service;
    /** @var DatabaseModelTransformer */
    private $model_transformer;
    /** @var MysqlDatabase[] */
    private $entities;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->entities = array();
    }

    /**
     * Add entity.
     *
     * @param MysqlDatabase $entity
     *
     * @return $this
     */
    public function addEntity(MysqlDatabase $entity)
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * Find and delete old users.
     */
    public function findAndDeleteOldUsers()
    {
        $users = $this
            ->getAdapter()
            ->getUsers()
            ->findByNameStartsWith($this->getDatabasePrefix());

        foreach ($users as $user) {
            /** @var User $user */
            if (is_numeric(substr($user->getName(), -1, 1))) {
                $entity = $this->findEntityByName($user->getName());

                if (!$entity) {
                    $this
                        ->getAdapter()
                        ->deleteUser($user);
                }
            }
        }
    }

    /**
     * Get Adapter.
     *
     * @return AdapterInterface
     */
    protected function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set Adapter.
     *
     * @param AdapterInterface $adapter
     *
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Get database prefix.
     *
     * @throws EmptyDatabasePrefixException
     * @return string
     */
    protected function getDatabasePrefix()
    {
        $prefix = $this
            ->getConfigService()
            ->getParameter('mysql.dbprefix');

        if (empty($prefix)) {
            throw new EmptyDatabasePrefixException();
        }

        return $prefix;
    }

    /**
     * Get ConfigService.
     *
     * @return ConfigService
     */
    protected function getConfigService()
    {
        return $this->config_service;
    }

    /**
     * Set ConfigService.
     *
     * @param ConfigService $config_service
     *
     * @return $this
     */
    public function setConfigService(ConfigService $config_service)
    {
        $this->config_service = $config_service;

        return $this;
    }

    /**
     * Find entity by name.
     *
     * @param string $name
     *
     * @return MysqlDatabase|null
     */
    protected function findEntityByName($name)
    {
        foreach ($this->entities as $entity) {
            if ($entity->getName() === $name) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * Create and update users and databases.
     */
    public function createAndUpdateUsersAndDatabases()
    {
        foreach ($this->entities as $entity) {
            /** @var MysqlDatabase $entity */

            $database       = $this
                ->getModelTransformer()
                ->transform($entity);
            $databaseExists = $this
                ->getAdapter()
                ->getDatabases();

            // User aktualisieren / anlegen
            foreach ($database->getUsers() as $user) {
                /** @var User $user */

                $usersExists = $this
                    ->getAdapter()
                    ->getUsers();

                if (!$usersExists->findByName($user->getName())) {
                    $this
                        ->getAdapter()
                        ->createUser($user);
                } else {
                    $this
                        ->getAdapter()
                        ->updateUser($user);
                }
            }

            // Datenbank aktualisieren / anlegen
            if ($databaseExists->findByName($database->getName())) {
                $this
                    ->getAdapter()
                    ->updateDatabase($database);
            } else {
                $this
                    ->getAdapter()
                    ->createDatabase($database);
            }
        }
    }

    /**
     * Get ModelTransformer.
     *
     * @return DatabaseModelTransformer
     */
    protected function getModelTransformer()
    {
        return $this->model_transformer;
    }

    /**
     * Set ModelTransformer.
     *
     * @param DatabaseModelTransformer $model_transformer
     *
     * @return $this
     */
    public function setModelTransformer(DatabaseModelTransformer $model_transformer)
    {
        $this->model_transformer = $model_transformer;

        return $this;
    }

    /**
     * Find and delete old databases.
     */
    public function findAndDeleteOldDatabases()
    {
        $databases = $this
            ->getAdapter()
            ->getDatabases()
            ->findByNameStartsWith($this->getDatabasePrefix());

        foreach ($databases as $database) {
            /** @var Database $database */
            if (is_numeric(substr($database->getName(), -1, 1))) {
                $entity = $this->findEntityByName($database->getName());

                if (!$entity) {
                    $this
                        ->getAdapter()
                        ->deleteDatabase($database);
                }
            }
        }
    }
}

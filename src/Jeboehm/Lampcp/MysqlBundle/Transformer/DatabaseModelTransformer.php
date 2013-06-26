<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Transformer;

use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\CoreBundle\Exception\WrongEncryptionKeyException;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;

/**
 * Class DatabaseModelTransformer
 *
 * Transform database entities to models which are
 * ready for the adapter.
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DatabaseModelTransformer
{
    /** @var CryptService */
    private $crypt_service;
    /** @var array */
    private $hosts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hosts = array(
            'localhost',
        );
    }

    /**
     * Transform MysqlDatabase entity to a model
     * which is ready for the adapter.
     *
     * @param MysqlDatabase $database
     *
     * @return Database
     */
    public function transform(MysqlDatabase $database)
    {
        $db_model = new Database();

        foreach ($this->hosts as $host) {
            $user_model = new User();
            $user_model
                ->setName($database->getName())
                ->setHost($host)
                ->setPassword($this->getPassword($database));

            $db_model->addUser($user_model);
        }

        $db_model->setName($database->getName());

        return $db_model;
    }

    /**
     * Decrypt and return the configured password.
     *
     * @param MysqlDatabase $database
     *
     * @return string
     */
    protected function getPassword(MysqlDatabase $database)
    {
        $enc = $database->getPassword();

        if ($this->getCryptService()) {
            try {
                $dec = $this
                    ->getCryptService()
                    ->decrypt($enc);

                return $dec;
            } catch (WrongEncryptionKeyException $e) {
            }
        }

        return $enc;
    }

    /**
     * Get CryptService.
     *
     * @return CryptService
     */
    protected function getCryptService()
    {
        return $this->crypt_service;
    }

    /**
     * Set CryptService.
     *
     * @param CryptService $crypt_service
     *
     * @return $this
     */
    public function setCryptService(CryptService $crypt_service)
    {
        $this->crypt_service = $crypt_service;

        return $this;
    }
}

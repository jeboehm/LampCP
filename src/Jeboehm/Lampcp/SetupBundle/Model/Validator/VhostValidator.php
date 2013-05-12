<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Model\Validator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Vhost;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class VhostValidator
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Validator
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostValidator extends AbstractValidator
{
    /** @var EntityManager */
    private $_em;

    /**
     * Validate vhost object.
     *
     * @param Vhost $vhost
     *
     * @return ValidationResult
     */
    public function validate(Vhost $vhost)
    {
        $result = new ValidationResult();
        $result->setSuccessful(true);
        $messages = array();

        if (!$this->validateUser($vhost->user)) {
            $messages[] = sprintf('User "%s" not found!', $vhost->user);
            $result->setSuccessful(false);
        }

        if (!$this->validateAddress($vhost->address)) {
            $messages[] = sprintf('Address "%s" is not valid!', $vhost->address);
            $result->setSuccessful(false);
        }

        if (!$this->validateIpAddress($vhost->ipaddress)) {
            $messages[] = sprintf('IP address "%s" is not valid!', $vhost->ipaddress);
            $result->setSuccessful(false);
        }

        if (!$result->getSuccessful()) {
            $result->setMessage($messages);
        }

        return $result;
    }

    /**
     * Check, that user exists.
     *
     * @param string $user
     *
     * @return bool
     */
    public function validateUser($user)
    {
        /** @var EntityRepository $repository */
        $repository = $this
            ->getEm()
            ->getRepository('JeboehmLampcpCoreBundle:User');

        /** @var User $entity */
        $entity = $repository->findOneBy(array('name' => $user));

        return $entity !== null;
    }

    /**
     * Get Em
     *
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->_em;
    }

    /**
     * Set Em
     *
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
    }

    /**
     * Validate address.
     *
     * @param string $address
     *
     * @return bool
     */
    public function validateAddress($address)
    {
        $validator  = Validation::createValidator();
        $violations = $validator->validateValue(
            $address,
            new Regex('/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i')
        );

        if ($violations->count() === 0) {
            return true;
        }

        return false;
    }

    /**
     * Validate ip address.
     *
     * @param string $ip
     *
     * @return bool
     */
    public function validateIpAddress($ip)
    {
        $validator  = Validation::createValidator();
        $violations = $validator->validateValue($ip, new Ip(array('version' => 'all')));

        if ($violations->count() === 0) {
            return true;
        }

        return false;
    }
}

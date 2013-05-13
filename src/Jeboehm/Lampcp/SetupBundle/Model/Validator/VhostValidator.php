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

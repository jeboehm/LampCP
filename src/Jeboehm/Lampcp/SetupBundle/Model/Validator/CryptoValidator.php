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

use Jeboehm\Lampcp\SetupBundle\Model\Form\Crypto;

/**
 * Class CryptoValidator
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Validator
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CryptoValidator extends AbstractValidator
{
    /**
     * Validate crypto form.
     *
     * @param Crypto $crypto
     *
     * @return ValidationResult
     */
    public function validate(Crypto $crypto)
    {
        $messages = array();
        $result   = new ValidationResult();
        $result->setSuccessful(true);

        if (empty($crypto->crypt)) {
            $result->setSuccessful(false);
            $messages[] = 'Encryption password must not be empty.';
        }

        if (empty($crypto->secret)) {
            $result->setSuccessful(false);
            $messages[] = 'Framework secret key must not be empty.';
        }

        if (count($messages) > 0) {
            $result->setMessage($messages);
        }

        return $result;
    }
}

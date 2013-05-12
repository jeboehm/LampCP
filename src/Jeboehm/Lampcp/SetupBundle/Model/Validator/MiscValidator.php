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

use Jeboehm\Lampcp\SetupBundle\Model\Form\Misc;

/**
 * Class MiscValidator
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Validator
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MiscValidator extends AbstractValidator
{
    /**
     * Validate misc form.
     *
     * @param Misc $misc
     *
     * @return ValidationResult
     */
    public function validate(Misc $misc)
    {
        $messages = array();
        $result   = new ValidationResult();
        $result->setSuccessful(true);

        if (!in_array($misc->language, array('de', 'en'))) {
            $messages[] = sprintf('Invalid language "%s"!', $misc->language);
            $result->setSuccessful(false);
        }

        if (count($messages) > 0) {
            $result->setMessage($messages);
        }

        return $result;
    }
}

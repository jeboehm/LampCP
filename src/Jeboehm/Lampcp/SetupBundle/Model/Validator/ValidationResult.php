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

/**
 * Class ValidationResult
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Validator
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ValidationResult
{
    /** @var bool */
    private $_successful;
    /** @var string */
    private $_message;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_successful = false;
        $this->_message    = '';
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->_message = $message;

        return $this;
    }

    /**
     * Get Successful
     *
     * @return boolean
     */
    public function getSuccessful()
    {
        return $this->_successful;
    }

    /**
     * Set Successful
     *
     * @param boolean $successful
     *
     * @return $this
     */
    public function setSuccessful($successful)
    {
        $this->_successful = $successful;

        return $this;
    }
}

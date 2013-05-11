<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Model\Annotation;

use Jeboehm\Lampcp\SetupBundle\Model\Exception\UnknownInputTypeException;

/**
 * Class Form
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Annotation
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 * @Annotation
 */
class Form extends AbstractAnnotation
{
    /** @var string */
    private $_message;
    /** @var string */
    private $_type;

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
     * Set message.
     *
     * @param string $message
     */
    protected function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @throws UnknownInputTypeException
     */
    protected function setType($type)
    {
        $valid = array(
            'ask',
            'askHiddenResponse',
        );

        if (!in_array($type, $valid)) {
            throw new UnknownInputTypeException();
        }

        $this->_type = $type;
    }
}

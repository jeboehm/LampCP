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

use Jeboehm\Lampcp\SetupBundle\Model\Exception\ForbiddenAnnotationNameException;

/**
 * Class AbstractAnnotation
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Annotation
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractAnnotation
{
    /** @var string */
    private $_name;

    /**
     * Constructor.
     *
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            $method = $this->getSetterMethodName($key);

            if (!method_exists($this, $method)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->{$method}($value);
        }
    }

    /**
     * Get setter method name.
     *
     * @param string $key
     *
     * @throws ForbiddenAnnotationNameException
     * @return string
     */
    private function getSetterMethodName($key)
    {
        if (strtolower($key) === 'name') {
            throw new ForbiddenAnnotationNameException();
        }

        return 'set' . ucfirst(strtolower($key));
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set Name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
}

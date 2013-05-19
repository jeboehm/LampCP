<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Model\Conversion;

use Doctrine\Common\Annotations\Reader;

/**
 * Class AnnotationConverter
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Reader
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class AnnotationConverter
{
    /** @var Reader */
    private $_reader;
    /** @var string */
    private $_annotation;

    /**
     * Constructor.
     *
     * @param Reader $reader
     * @param string $annotation
     */
    public function __construct(Reader $reader, $annotation)
    {
        $this->_reader     = $reader;
        $this->_annotation = $annotation;
    }

    /**
     * Form annotation converter.
     *
     * @param object $form
     *
     * @return array
     */
    public function convert($form)
    {
        $data       = array();
        $reflection = new \ReflectionObject($form);

        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->_reader->getPropertyAnnotation($property, $this->_annotation);

            if ($annotation !== null) {
                if (method_exists($annotation, 'setName')) {
                    $annotation->setName($property->getName());
                }

                $data[] = $annotation;
            }
        }

        return $data;
    }
}

<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Model\Transformer;

use Jeboehm\Lampcp\SetupBundle\Model\Form\AbstractForm;

/**
 * Class ParametersYamlTransformer
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ParametersYamlTransformer
{
    /** @var AbstractForm[] */
    private $_forms;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_forms = array();
    }

    /**
     * Add form.
     *
     * @param AbstractForm $form
     *
     * @return $this
     */
    public function addForm(AbstractForm $form)
    {
        $this->_forms[] = $form;
        return $this;
    }

    /**
     * Render YAML File.
     *
     * @return string
     */
    public function renderYaml()
    {
        $collection = $this->_collectKeyValue();
        $keyLength  = $this->_getMaxKeyLength($collection) + 2;
        $output     = array();
        $output[]   = 'parameters:';

        foreach ($collection as $key => $value) {
            $key      = str_pad($key . ': ', $keyLength, ' ');
            $output[] = sprintf('    %s%s', $key, $value);
        }

        return join(PHP_EOL, $output);
    }

    /**
     * Collect key => value.
     *
     * @return array
     */
    protected function _collectKeyValue()
    {
        $collection = array();

        foreach ($this->getForms() as $form) {
            foreach ($this->getProperties($form) as $key => $value) {
                $collection[$key] = $value;
            }
        }

        return $collection;
    }

    /**
     * Get forms.
     *
     * @return AbstractForm[]
     */
    public function getForms()
    {
        return $this->_forms;
    }

    /**
     * Get properties.
     *
     * @param AbstractForm $form
     *
     * @return array
     */
    public function getProperties(AbstractForm $form)
    {
        return get_object_vars($form);
    }

    /**
     * Get max key length.
     *
     * @param array $collection
     *
     * @return int
     */
    protected function _getMaxKeyLength(array $collection)
    {
        $max = 0;

        foreach (array_keys($collection) as $key) {
            $max = max(strlen($key), $max);
        }

        return $max;
    }
}

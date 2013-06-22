<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType as ParentClassType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractType
 *
 * Provides some useful methods for form types
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
abstract class AbstractType extends ParentClassType
{
    /**
     * Determine, if form is in editing mode
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return bool
     */
    protected function _getIsEditMode(FormBuilderInterface $builder)
    {
        if (!$builder
            ->getData()
            ->getId()
        ) {
            return false;
        }

        return true;
    }
}

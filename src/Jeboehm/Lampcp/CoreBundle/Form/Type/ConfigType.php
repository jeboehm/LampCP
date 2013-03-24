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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType {
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('configentities', 'collection', array(
                                                           'type'      => new ConfigEntityType(),
                                                           'prototype' => false,
                                                      ));
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName() {
        return 'jeboehm_lampcp_configbundle_configtype';
    }
}

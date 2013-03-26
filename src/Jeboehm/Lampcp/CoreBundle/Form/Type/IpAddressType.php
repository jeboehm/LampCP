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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IpAddressType
 *
 * Builds a ip address form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class IpAddressType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('alias')
            ->add('ip')
            ->add('port')
            ->add('hasSsl', null, array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                                    'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\IpAddress'
                               ));
    }

    public function getName() {
        return 'jeboehm_lampcp_corebundle_ipaddresstype';
    }
}

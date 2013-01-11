<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class DomainSelectorType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('domain', 'entity', array(
										   'class'       => 'JeboehmLampcpCoreBundle:Domain',
										   'property'    => 'domain',
										   'empty_value' => '----',
									  ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_domainselectortype';
	}
}

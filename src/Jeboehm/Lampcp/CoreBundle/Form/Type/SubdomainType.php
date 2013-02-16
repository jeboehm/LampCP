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

class SubdomainType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('subdomain')
			->add('path', null, array('required' => false))
			->add('certificate', 'entity', array(
												'class'    => 'JeboehmLampcpCoreBundle:Certificate',
												'property' => 'name',
												'required' => false,
										   ))
			->add('forceSsl', null, array('required' => false))
			->add('isWildcard', null, array('required' => false))
			->add('parsePhp', null, array('required' => false))
			->add('customconfig', null, array('required' => false));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Subdomain'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_subdomaintype';
	}
}

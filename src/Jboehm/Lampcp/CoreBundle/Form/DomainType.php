<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('domain')
			->add('path', null, array('read_only' => true))
			->add('webroot')
			->add('user', 'entity', array(
										 'class'    => 'JboehmLampcpCoreBundle:User',
										 'property' => 'name',
									))
			->add('ipaddress', 'entity', array(
											  'class'    => 'JboehmLampcpCoreBundle:IpAddress',
											  'property' => 'alias',
											  'multiple' => true,
											  'required' => false,
										 ))
			->add('customconfig', null, array('required' => false));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\Domain'
							   ));
	}

	public function getName() {
		return 'jboehm_lampcp_corebundle_domaintype';
	}
}

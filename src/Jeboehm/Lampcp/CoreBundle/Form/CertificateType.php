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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CertificateType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', null, array('required' => true))
			->add('CertificateFile', null, array('required' => true))
			->add('CertificateKeyFile', null, array('required' => false))
			->add('CertificateChainFile', null, array('required' => false))
			->add('CACertificateFile', null, array('required' => false));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Certificate'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_certificatetype';
	}
}

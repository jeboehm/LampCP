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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProtectionUserType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username');

		if($this->_isEditMode) {
			$builder->add('password', 'repeated', array(
													   'type'     => 'password',
													   'required' => false,
												  ));
		} else {
			$builder->add('password', 'repeated', array(
													   'type'     => 'password',
													   'required' => true,
												  ));
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\ProtectionUser'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_protectionusertype';
	}
}

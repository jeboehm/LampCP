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

class AdminType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email');

		if($this->_getIsEditMode()) {
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
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Admin'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_admintype';
	}
}

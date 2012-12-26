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

class AdminType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email');

		if($this->_getIsEditMode()) {
			$builder->add('password', 'password', array('required' => false));
		} else {
			$builder->add('password', 'password', array('required' => true));
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\Admin'
							   ));
	}

	public function getName() {
		return 'jboehm_lampcp_corebundle_admintype';
	}
}

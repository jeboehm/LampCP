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

class MysqlDatabaseType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name')
			->add('comment');

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
									'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\MysqlDatabase'
							   ));
	}

	public function getName() {
		return 'jboehm_lampcp_corebundle_mysqldatabasetype';
	}
}

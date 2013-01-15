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

class MailAddressType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$forwardType = new MailForwardType();

		$builder->add('address');

		if($this->_getIsEditMode()) {
			$builder->add('mailforward', 'collection', array(
															'type'         => $forwardType,
															'allow_add'    => true,
															'allow_delete' => true,
															'by_reference' => false,
													   ));
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\MailAddress'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_mailaddresstype';
	}
}

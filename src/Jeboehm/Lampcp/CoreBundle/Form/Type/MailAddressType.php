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

class MailAddressType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$mailForwardType = new MailForwardType();
		$mailAccountType = new MailAccountType();

		$builder
			->add('address')
			->add('mailforward', 'collection', array(
													'type'         => $mailForwardType,
													'allow_add'    => true,
													'allow_delete' => true,
													'by_reference' => false,
											   ))
			->add('mailaccount', $mailAccountType);

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

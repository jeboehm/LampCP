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

class MailForwardType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('target', null, array(
										   'label' => '',
									  ));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\MailForward'
							   ));
	}

	public function getName() {
		return 'jeboehm_lampcp_corebundle_mailforwardtype';
	}
}

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
	/** @var array */
	protected $_uids;

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('domain')
			->add('uid', 'choice', array('choices' => $this->_uids))
			->add('path')
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

	/**
	 * @param bool  $edit
	 * @param array $uids
	 */
	public function __construct($edit = false, array $uids) {
		parent::__construct($edit);

		$this->_uids = $uids;
	}
}

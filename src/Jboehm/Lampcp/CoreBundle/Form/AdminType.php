<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminType extends AbstractType {
	protected $_isEditMode = false;

	/**
	 * @param bool $edit
	 */
	public function __construct($edit = false) {
		$this->_isEditMode = $edit;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email');

		if($this->_isEditMode) {
			$builder->add('password', null, array('required' => false));
		} else {
			$builder->add('password', null, array('required' => true));
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

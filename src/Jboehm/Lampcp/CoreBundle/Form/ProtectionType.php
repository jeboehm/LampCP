<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProtectionType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('path')
			->add('realm')
			->add('username')
			->add('password', null, array('required' => false));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\Protection'
							   ));
	}

	public function getName() {
		return 'jboehm_lampcp_corebundle_protectiontype';
	}
}

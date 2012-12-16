<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('domain')
			->add('hasWeb', null, array('required' => false))
			->add('hasMail', null, array('required' => false))
			->add('hasSSH', null, array('required' => false))
			->add('path');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
									'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\Domain'
							   ));
	}

	public function getName() {
		return 'jboehm_lampcp_corebundle_domaintype';
	}
}

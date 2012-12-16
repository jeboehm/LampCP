<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MysqlDatabaseType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name')
			->add('comment')
			->add('password');
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

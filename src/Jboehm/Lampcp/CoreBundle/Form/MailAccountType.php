<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('uid')
            ->add('quota')
            ->add('hasPop3')
            ->add('hasImap4')
            ->add('hasSmtp')
            ->add('enabled')
            ->add('domain')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\MailAccount'
        ));
    }

    public function getName()
    {
        return 'jboehm_lampcp_corebundle_mailaccounttype';
    }
}

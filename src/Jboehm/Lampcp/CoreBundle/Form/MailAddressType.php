<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address')
            ->add('hasCatchAll')
            ->add('domain')
            ->add('mailaccount')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\MailAddress'
        ));
    }

    public function getName()
    {
        return 'jboehm_lampcp_corebundle_mailaddresstype';
    }
}

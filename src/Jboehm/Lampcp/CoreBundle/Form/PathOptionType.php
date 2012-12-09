<?php

namespace Jboehm\Lampcp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PathOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path')
            ->add('hasDirectoryListing')
            ->add('error404')
            ->add('error403')
            ->add('error500')
            ->add('domain')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jboehm\Lampcp\CoreBundle\Entity\PathOption'
        ));
    }

    public function getName()
    {
        return 'jboehm_lampcp_corebundle_pathoptiontype';
    }
}

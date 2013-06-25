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

use Jeboehm\Lampcp\CoreBundle\Form\Model\AdminRoles;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AdminType
 *
 * Builds an admin form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class AdminType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email', 'email')
            ->add(
                'plainPassword',
                'repeated',
                array(
                     'type'     => 'password',
                     'required' => false,
                )
            )
            ->add(
                'roles',
                'choice',
                array(
                     'multiple' => true,
                     'choices'  => AdminRoles::$roles,
                )
            )
            ->add('enabled', 'checkbox');
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Admin'
            )
        );
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'jeboehm_lampcp_corebundle_admintype';
    }
}

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

/**
 * Class DomainType
 *
 * Builds a domain form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'domain',
                null,
                array(
                     'read_only' => $this->_getIsEditMode($builder)
                )
            )
            ->add(
                'parent',
                'entity',
                array(
                     'class'    => 'JeboehmLampcpCoreBundle:Domain',
                     'property' => 'domain',
                     'required' => false,
                )
            )
            ->add('path', null, array('read_only' => true))
            ->add('webroot')
            ->add(
                'redirectUrl',
                null,
                array(
                     'required' => false,
                     'attr'     => array(
                         'help_block' => 'jeboehm.lampcp.corebundle.domaintype.redirectUrl.help',
                     )
                )
            )
            ->add(
                'user',
                'entity',
                array(
                     'class'    => 'JeboehmLampcpCoreBundle:User',
                     'property' => 'name',
                )
            )
            ->add(
                'ipaddress',
                'entity',
                array(
                     'class'    => 'JeboehmLampcpCoreBundle:IpAddress',
                     'property' => 'alias',
                     'multiple' => true,
                     'required' => false,
                )
            )
            ->add(
                'certificate',
                'entity',
                array(
                     'class'    => 'JeboehmLampcpCoreBundle:Certificate',
                     'property' => 'name',
                     'required' => false,
                )
            )
            ->add('forceSsl', null, array('required' => false))
            ->add('isWildcard', null, array('required' => false))
            ->add('parsePhp', null, array('required' => false))
            ->add('customconfig', null, array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Domain'
            )
        );
    }

    public function getName()
    {
        return 'jeboehm_lampcp_corebundle_domaintype';
    }
}

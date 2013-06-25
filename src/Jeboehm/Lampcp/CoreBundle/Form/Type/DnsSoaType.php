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
 * Class DnsType
 *
 * Builds a DNS-SOA configuration form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DnsSoaType extends AbstractType
{
    /**
     * Build form
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('primary')
            ->add('mail')
            ->add(
                'serial',
                'integer',
                array(
                     'read_only' => true,
                )
            )
            ->add('refresh', 'integer')
            ->add('retry', 'integer')
            ->add('expire', 'integer')
            ->add('minimum', 'integer');
    }

    /**
     * Set default options
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class' => 'Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\SOA'
            )
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'jeboehm_lampcp_corebundle_dnssoatype';
    }
}

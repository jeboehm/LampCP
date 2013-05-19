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

use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\DnsResourceRecordTypes;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DnsResourceRecordType
 *
 * Builds a form for DNS resource records
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DnsResourceRecordType extends AbstractType {
    /**
     * Build form
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text', array(
                                       'required' => false,
                                       'attr'     => array(
                                           'placeholder' => 'jeboehm.lampcp.corebundle.dnsresourcerecordtype.record.name'
                                       )
                                  ))
            ->add('ttl', 'integer', array(
                                         'attr' => array(
                                             'class'       => 'input-mini',
                                             'placeholder' => 'jeboehm.lampcp.corebundle.dnsresourcerecordtype.record.ttl'
                                         )
                                    ))
            ->add('type', 'choice', array(
                                         'choice_list' => $this->_getDnsTypes(),
                                         'attr'        => array(
                                             'class'       => 'input-medium',
                                             'placeholder' => 'jeboehm.lampcp.corebundle.dnsresourcerecordtype.record.type'
                                         )
                                    ))
            ->add('rdata', 'text', array(
                                        'attr' => array(
                                            'placeholder' => 'jeboehm.lampcp.corebundle.dnsresourcerecordtype.record.rdata'
                                        )
                                   ))
            ->add('position', 'hidden', array(
                                             'attr' => array(
                                                 'class' => 'dnsposition',
                                             )
                                        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return 'jeboehm_lampcp_corebundle_dnsresourcerecordtype';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     *
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                                    'data_class' => 'Jeboehm\Lampcp\CoreBundle\Form\Model\DnsResourceModel',
                               ));
    }

    /**
     * Get choice list
     *
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList
     */
    protected function _getDnsTypes() {
        $list = new ChoiceList(DnsResourceRecordTypes::$types, DnsResourceRecordTypes::$types);

        return $list;
    }
}

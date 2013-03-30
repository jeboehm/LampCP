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
use Jeboehm\Lampcp\CoreBundle\Form\Transformer\ZoneCollectionTransformer;

/**
 * Class DnsType
 *
 * Builds a DNS configuration form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DnsType extends AbstractType {
    /**
     * Build form
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var $domain \Jeboehm\Lampcp\CoreBundle\Entity\Domain */
        $domain                    = $builder
            ->getData()
            ->getDomain();
        $zoneCollectionTransformer = new ZoneCollectionTransformer($builder
            ->getData()
            ->getZoneCollection());
        $dnsResourceRecordType     = new DnsResourceRecordType();

        $builder
            ->add('subdomain', null, array(
                                          'required' => false,
                                          'attr'     => array(
                                              'append_input' => '.' . $domain->getDomain()
                                          )
                                     ))
            ->add($builder
                ->create('zone_collection', 'collection', array(
                                                               'type'         => $dnsResourceRecordType,
                                                               'allow_add'    => true,
                                                               'allow_delete' => true,
                                                               'options'      => array(
                                                                   'required' => false
                                                               )
                                                          ))
                ->addModelTransformer($zoneCollectionTransformer));
    }

    /**
     * Set default options
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                                    'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\Dns'
                               ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return 'jeboehm_lampcp_corebundle_dnstype';
    }
}

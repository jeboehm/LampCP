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
 * Class MailAddressType
 *
 * Builds a mail address form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MailAddressType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var $domain \Jeboehm\Lampcp\CoreBundle\Entity\Domain */
        $mailForwardType = new MailForwardType();
        $mailAccountType = new MailAccountType();
        $domain          = $builder
            ->getData()
            ->getDomain();

        $builder
            ->add('address', null, array(
                                        'attr' => array(
                                            'append_input' => '@' . $domain->getDomain()
                                        )
                                   ))
            ->add('mailforward', 'collection', array(
                                                    'type'         => $mailForwardType,
                                                    'allow_add'    => true,
                                                    'allow_delete' => true,
                                                    'by_reference' => false,
                                               ))
            ->add('mailaccount', $mailAccountType);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                                    'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\MailAddress'
                               ));
    }

    public function getName() {
        return 'jeboehm_lampcp_corebundle_mailaddresstype';
    }
}

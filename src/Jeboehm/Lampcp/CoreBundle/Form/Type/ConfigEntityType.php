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

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\CoreBundle\Form\Model\ConfigEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ConfigEntityType
 *
 * Builds the form for config entities
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigEntityType extends AbstractType {
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        /**
         * Formularelemente über Event hinzufügen,
         * damit die Typen bestimmt werden können
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof ConfigEntity) {
                switch ($data->getType()) {
                    case ConfigTypes::TYPE_BOOL:
                        $type  = 'checkbox';
                        $value = (bool)$data->getValue();
                        break;

                    default:
                        $type  = 'text';
                        $value = $data->getValue();
                }

                $form->add($builder
                    ->getFormFactory()
                    ->createNamed('value', $type, $value, array(
                                                               'required' => false,
                                                               'auto_initialize' => false,
                                                          )));
            }
        });
    }

    /**
     * Set options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                                    'data_class' => 'Jeboehm\Lampcp\CoreBundle\Form\Model\ConfigEntity',
                               ));
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName() {
        return 'jeboehm_lampcp_configbundle_configtype';
    }
}

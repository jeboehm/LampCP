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
 * Class PathOptionType
 *
 * Builds a path option form
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Type
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class PathOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $domain \Jeboehm\Lampcp\CoreBundle\Entity\Domain */
        $domain = $builder
            ->getData()
            ->getDomain();

        $builder
            ->add(
                'path',
                null,
                array(
                     'required' => false,
                     'attr'     => array(
                         'prepend_input' => $domain->getPath() . '/'
                     )
                )
            )
            ->add('hasDirectoryListing', null, array('required' => false))
            ->add('error404', null, array('required' => false))
            ->add('error403', null, array('required' => false))
            ->add('error500', null, array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class' => 'Jeboehm\Lampcp\CoreBundle\Entity\PathOption'
            )
        );
    }

    public function getName()
    {
        return 'jeboehm_lampcp_corebundle_pathoptiontype';
    }
}

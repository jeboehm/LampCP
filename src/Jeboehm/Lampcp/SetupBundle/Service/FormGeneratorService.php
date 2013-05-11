<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Service;

use Doctrine\Common\Annotations\Reader;
use Jeboehm\Lampcp\SetupBundle\Model\Annotation\AbstractAnnotation;
use Jeboehm\Lampcp\SetupBundle\Model\Annotation\Form;
use Jeboehm\Lampcp\SetupBundle\Model\Conversion\AnnotationConverter;
use Jeboehm\Lampcp\SetupBundle\Model\Form\AbstractForm;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FormGeneratorService
 *
 * @package Jeboehm\Lampcp\SetupBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FormGeneratorService
{
    /** @var OutputInterface */
    private $_output;
    /** @var DialogHelper */
    private $_dialog;
    /** @var Reader */
    private $_reader;
    /** @var string */
    private $_annotationClass;

    /**
     * Process form.
     *
     * @param AbstractForm $form
     *
     * @return array
     */
    public function processForm(AbstractForm $form)
    {
        $data = array();

        foreach ($this->getFormAnnotations($form) as $annotation) {
            /** @var Form $annotation */
            $name    = $annotation->getName();
            $type    = $annotation->getType();
            $message = $annotation->getMessage() . ' > ';

            $form->{$name} = $this
                ->getDialog()
                ->{$type}(
                    $this->getOutput(),
                    $message
                );
        }

        return $data;
    }

    /**
     * Get form annotations.
     *
     * @param AbstractForm $form
     *
     * @return AbstractAnnotation[]
     */
    public function getFormAnnotations(AbstractForm $form)
    {
        $annotations = $this
            ->getAnnotationConverter()
            ->convert($form);

        return $annotations;
    }

    /**
     * Get annotation converter.
     *
     * @return AnnotationConverter
     */
    public function getAnnotationConverter()
    {
        $converter = new AnnotationConverter($this->getReader(), $this->getAnnotationClass());
        return $converter;
    }

    /**
     * Get Reader
     *
     * @return Reader
     */
    public function getReader()
    {
        return $this->_reader;
    }

    /**
     * Set Reader
     *
     * @param Reader $reader
     *
     * @return $this
     */
    public function setReader(Reader $reader)
    {
        $this->_reader = $reader;

        return $this;
    }

    /**
     * Get AnnotationClass
     *
     * @return string
     */
    public function getAnnotationClass()
    {
        return $this->_annotationClass;
    }

    /**
     * Set AnnotationClass
     *
     * @param string $annotationClass
     *
     * @return $this
     */
    public function setAnnotationClass($annotationClass)
    {
        $this->_annotationClass = $annotationClass;

        return $this;
    }

    /**
     * Get Dialog
     *
     * @return DialogHelper
     */
    public function getDialog()
    {
        return $this->_dialog;
    }

    /**
     * Set Dialog
     *
     * @param DialogHelper $dialog
     *
     * @return $this
     */
    public function setDialog(DialogHelper $dialog)
    {
        $this->_dialog = $dialog;

        return $this;
    }

    /**
     * Get Output
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Set Output
     *
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput($output)
    {
        $this->_output = $output;

        return $this;
    }
}

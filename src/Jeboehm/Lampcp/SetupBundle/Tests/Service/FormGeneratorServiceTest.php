<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Tests\Service;

use Doctrine\Common\Annotations\Reader;
use Jeboehm\Lampcp\SetupBundle\Model\Form\Database;
use Jeboehm\Lampcp\SetupBundle\Service\FormGeneratorService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class FormGeneratorServiceTest
 *
 * @package Jeboehm\Lampcp\SetupBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FormGeneratorServiceTest extends WebTestCase
{
    /**
     * Test getFormAnnotations().
     */
    public function testGetFormAnnotations()
    {
        $annotations = $this
            ->getService()
            ->getFormAnnotations(new Database());

        $this->assertCount(4, $annotations);
    }

    /**
     * Get service.
     *
     * @return FormGeneratorService
     */
    protected function getService()
    {
        /** @var Reader $reader */
        $reader = $this
            ->createClient()
            ->getContainer()
            ->get('annotation_reader');

        $service = new FormGeneratorService();
        $service
            ->setAnnotationClass('Jeboehm\Lampcp\SetupBundle\Model\Annotation\Form')
            ->setReader($reader);
        return $service;
    }

    /**
     * Test dialog getter / setter.
     */
    public function testDialogGetSet()
    {
        $dialog  = new DialogHelper();
        $service = $this->getFormGeneratorService();

        $service->setDialog($dialog);
        $this->assertEquals($dialog, $service->getDialog());
    }

    /**
     * @return FormGeneratorService
     */
    protected function getFormGeneratorService()
    {
        /** @var Reader $reader */
        $reader = $this
            ->createClient()
            ->getContainer()
            ->get('annotation_reader');

        $service = new FormGeneratorService();
        $service
            ->setAnnotationClass('Jeboehm\Lampcp\SetupBundle\Model\Annotation\Form')
            ->setReader($reader);
        return $service;
    }

    /**
     * Test output getter / setter.
     */
    public function testOutputGetSet()
    {
        $output  = new ConsoleOutput();
        $service = $this->getFormGeneratorService();

        $service->setOutput($output);
        $this->assertEquals($output, $service->getOutput());
    }
}

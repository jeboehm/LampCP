<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Tests\Model\Transformer;

use Jeboehm\Lampcp\SetupBundle\Model\Form\Database;
use Jeboehm\Lampcp\SetupBundle\Model\Transformer\ParametersYamlTransformer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ParametersYamlTransformerTest
 *
 * @package Jeboehm\Lampcp\SetupBundle\Tests\Model\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ParametersYamlTransformerTest extends WebTestCase
{
    /**
     * Test getProperties().
     */
    public function testGetProperties()
    {
        $form   = new Database();
        $result = $this
            ->getTransformer()
            ->getProperties($form);

        $this->assertCount(4, $result);
        $this->assertArrayHasKey('database_host', $result);
        $this->assertArrayHasKey('database_name', $result);
        $this->assertArrayHasKey('database_user', $result);
        $this->assertArrayHasKey('database_password', $result);
    }

    /**
     * Get transformer.
     *
     * @return ParametersYamlTransformer
     */
    public function getTransformer()
    {
        /** @var ParametersYamlTransformer $service */
        $service = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_setup.model.transformer.parametersyamltransformer');

        return $service;
    }

    /**
     * Test renderYaml().
     */
    public function testRenderYaml()
    {
        $service                     = $this->getTransformer();
        $database                    = new Database();
        $database->database_host     = 'localhost';
        $database->database_name     = 'lampcp';
        $database->database_user     = 'jeff';
        $database->database_password = 'test';

        $result = $service
            ->addForm($database)
            ->renderYaml();

        $this->assertContains('database_host', $result);
        $this->assertContains('localhost', $result);
        $this->assertContains('database_password', $result);
        $this->assertContains('jeff', $result);
    }
}

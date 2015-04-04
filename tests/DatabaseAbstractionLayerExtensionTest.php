<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Tests;

use Arlecchino\DatabaseAbstractionLayer\DatabaseAbstractionLayerCompilerPass;
use Arlecchino\DatabaseAbstractionLayer\DatabaseAbstractionLayerExtension;
use Arlecchino\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class DatabaseAbstractionLayerExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the load method
     * and make sure the altered container is still dumpable.
     *
     * @covers Arlecchino\DatabaseAbstractionLayer\DatabaseAbstractionLayerExtension::load
     */
    public function testLoad()
    {
        $extension = new DatabaseAbstractionLayerExtension();

        $container = new ContainerBuilder();

        $extension->load(
            array(
                array(
                    'dbal' => array(),
                ),
            ),
            $container
        );

        $container->compile();

        $resource = $container->getResources()[0];
        /* @var $resource FileResource */

        $this->assertSame(
            realpath(
                sprintf(
                    '%s/../services.xml',
                    __DIR__
                )
            ),
            $resource->getResource()
        );

        $this->assertSame(
            array(),
            $container->getParameter(
                'dbal.parameters_by_database_connection_name'
            )
        );

        $dumper = new PhpDumper(
            $container
        );

        $this->assertTrue(
            is_string(
                $dumper->dump()
            )
        );

        $this->assertInstanceOf(
            DatabaseConnectionManager::class,
            $container->get(
                'dbal.manager.database_connection'
            )
        );
    }

    /**
     * @covers Arlecchino\DatabaseAbstractionLayer\DatabaseAbstractionLayerExtension::getCompilerPasses
     */
    public function testGetCompilerPasses()
    {
        $extension = new DatabaseAbstractionLayerExtension();

        $passes = $extension->getCompilerPasses();

        $this->assertInstanceOf(
            DatabaseAbstractionLayerCompilerPass::class,
            $passes[0]
        );
    }
}
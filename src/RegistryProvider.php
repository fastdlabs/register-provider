<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace LinghitExts\RegistryProvider;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\RegistryProvider\Console\RegistryCommand;

/**
 * Class Sentinel
 * @package ServiceProvider\Sentinel
 */
class RegistryProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed
     */
    public function register(Container $container)
    {
        config()->merge([
            'consoles' => [
                RegistryCommand::class,
            ],
        ]);
    }
}

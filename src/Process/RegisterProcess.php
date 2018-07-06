<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\RegistryProvider\Process;


use FastD\Process\AbstractProcess;
use FastD\RegistryProvider\Client\Register;
use RuntimeException;
use swoole_process;

/**
 * Class SentinelProcess
 * @package ServiceProvider\Sentinel\Process
 */
class RegisterProcess extends AbstractProcess
{
    /**
     * @param swoole_process $swoole_process
     * @return callable|void
     */
    public function handle(swoole_process $swoole_process)
    {
        if (!config()->has('registry')){
            throw new RuntimeException(sprintf('register address url cannot be setting.'));
        }

        $client = new Register(config()->get('registry'));

        $client->start();
    }
}
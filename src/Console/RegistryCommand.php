<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\RegistryProvider\Console;

use FastD\Packet\Json;
use FastD\Servitization\Client\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SentinelCommand
 * @package ServiceProvider\Sentinel\Console
 */
class RegistryCommand extends Command
{
    /**
     * @var Client
     */
    protected $client;

    protected $uris = [
        'list'      => ['GET', '/services'],
        'show'      => ['GET', '/services/'],
        'del'       => ['DELETE', '/services/'],
        'stop'      => ['PUT', '/services/'],
        'status'    => ['GET', '/status'],
    ];

    protected function configure()
    {
        $this->setName('registry');
        $this->addArgument('action');
        $this->addArgument('name', InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \FastD\Packet\Exceptions\PacketException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        if (!array_key_exists($action, $this->uris)) {
            throw new \RuntimeException(sprintf('Unsupport action %s', $action));
        }

        $this->client = new Client(config()->get('registry.host'));

        $this->client->connect();

        $response = $this->client->send(Json::encode([
            'method' => $this->uris[$action][0],
            'path' => $this->uris[$action][1].$input->getArgument('name')
        ]));

        $response = Json::decode((string) $response->getBody());

        $output->writeln(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

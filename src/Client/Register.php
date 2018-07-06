<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\RegistryProvider\Client;


use FastD\Packet\Json;
use FastD\RegistryProvider\ServerStatus;
use FastD\Swoole\Client;
use swoole_client;

/**
 * Class Alive
 * @package ServiceProvider\Sentinel
 */
class Register extends Client
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * Alive constructor.
     * @param array $config
     */
    public function __construct(array $config, $async = true, $keep_alive = false)
    {
        parent::__construct($config['host'], $async, $keep_alive);
    }

    /**
     * @param swoole_client $client
     * @return mixed|void
     * @throws \FastD\Packet\Exceptions\PacketException
     */
    public function onConnect(swoole_client $client)
    {
        $packet = Json::encode([
            'method' => 'POST',
            'path' => '/services',
            'args' => ServerStatus::make()->getArrayCopy()
        ]);

        $client->send($packet);
    }

    public function onReceive(swoole_client $client, $data)
    {
        $data = Json::decode($data);
        $data = json_encode($data, JSON_PRETTY_PRINT);
        echo "接收信息: ".$data.PHP_EOL;
    }

    public function onError(swoole_client $client)
    {
        echo '连接失败'.PHP_EOL;
        //服务注册失败稍后再试
        $this->timeAfter();
    }

    public function onClose(swoole_client $client)
    {
        echo '连接断开'.PHP_EOL;
        //服务注册断开稍后再试
        $this->timeAfter();
    }
}
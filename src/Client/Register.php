<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace LinghitExts\RegistryProvider\Client;

use FastD\Packet\Json;
use FastD\Swoole\Client;
use LinghitExts\RegistryProvider\ServerStatus;
use swoole_client;

/**
 * Class Register
 * @package ServiceProvider\RegisterProvider
 */
class Register extends Client
{
    protected $try_count = 0;
    protected $error_count = 0;
    protected $max_try_count = 10;

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
     * 断线重连
     */
    public function tryReconnect()
    {
        if ($this->try_count <= $this->max_try_count) {
            echo 'try connecting: ' . $this->try_count . PHP_EOL;
            $this->connect();
            $this->try_count++;
            // 休眠时间递增
            sleep($this->try_count * 2 - 1);
        }
    }

    /**
     * 向注册中心注册服务节点
     *
     * @param swoole_client $client
     * @return mixed|void
     * @throws \FastD\Packet\Exceptions\PacketException
     */
    public function onConnect(swoole_client $client)
    {
        $this->try_count = 0;

        // 定时上报最新数据
        timer_tick(5000, function ($id) use ($client) {
            $packet = Json::encode([
                'method' => 'POST',
                'path' => '/services',
                'args' => ServerStatus::make()->getArrayCopy()
            ]);
            if ($this->client->isConnected() && false !== $client->send($packet)) {
                $this->try_count = 0;
            } else {
                timer_clear($id);
            }
        });
    }

    /**
     * 向服务发现注册服务节点，对接收的信息不产生处理。
     *
     * @param swoole_client $client
     * @param string $data
     * @return mixed|void
     */
    public function onReceive(swoole_client $client, $data)
    {
    }

    /**
     * 输出错误信息
     * 判断错误信息发起重连
     *
     * @param swoole_client $client
     * @return mixed|void
     */
    public function onError(swoole_client $client)
    {
        $this->tryReconnect();
    }

    /**
     * 连接关闭，发起重连
     *
     * @param swoole_client $client
     * @return mixed|void
     */
    public function onClose(swoole_client $client)
    {
        $this->tryReconnect();
    }
}

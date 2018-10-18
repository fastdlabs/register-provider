<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\RegistryProvider;

use FastD\Http\Uri;
use FastD\Utils\ArrayObject;

/**
 * Class ServerStatus
 * @package ServiceProvider\Sentinel
 */
class ServerStatus extends ArrayObject
{

    public function __construct()
    {
        //配置不存在加载
        if (!config()->has('server')) {
            config()->merge([
                'server' => load(app()->getPath() . '/config/server.php'),
            ]);
        }

        $host = config()->get('server.host');
        $uri = new Uri($host);
        $config['ip'] = get_local_ip();
        $config['environment'] = config()->get('environment', 'develop');
        $config['service_name'] = config()->get('name');
        $config['service_name'] = config()->get('name');
        $config['service_host'] = '0.0.0.0' === $uri->getHost() ? get_local_ip() : $uri->getHost();
        $config['service_protocol'] = ('' == $uri->getScheme() ? 'http' : $uri->getScheme());
        $config['service_port'] = $uri->getPort();
        $config['service_pid'] = !file_exists(config()->get('server.options.pid_file')) ?
            '' : file_get_contents(config()->get('server.options.pid_file'));
        $config['server']['options'] = config()->get('server.options');
        $config['status'] = $this->flushState();
        $config['routes'] = $this->routes();

        parent::__construct($config);
    }

    public function routes()
    {
        $routes = [];
        foreach (route()->aliasMap as $map) {
            foreach ($map as $key => $route) {
                $routes[$key] = [$route->getMethod(), $route->getPath()];
            }

        }
        return $routes;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return json_encode($this->getArrayCopy());
    }

    /**
     * @return array
     */
    public function flushState()
    {
        return app()->has('server') ? swoole()->stats() : [];
    }
}

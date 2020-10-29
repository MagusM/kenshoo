<?php

namespace Kenshoo;

use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\LimitingServer;
use React\Socket\Server;

class ServerSingleton {
    private static $instance = null;

    private $loop;
    private $server;

    private function __construct($ip = '127.0.0.1', $port = '1111')
    {
        $this->loop = Factory::create();
        $this->server = new LimitingServer(new Server("$ip:$port", $this->loop), null);
        $this->server->on('error', function (\Exception $e) {
            echo 'error: ' . $e->getMessage() . PHP_EOL;
        });
    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new ServerSingleton();
        }

        return self::$instance;
    }

    public function getServer() {
        return $this->server;
    }

    public function close() {
        echo "Shutting down server {$this->server->getAddress()}" . PHP_EOL;
        $this->server->close();
    }

    public function pause() {
        $this->server->pause();
    }

    public function resume() {
        $this->server->resume();
    }

    public function run() {
        return $this->loop->run();
    }
}
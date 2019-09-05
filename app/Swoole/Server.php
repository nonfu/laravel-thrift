<?php
namespace App\Swoole;

use Swoole\Server as SwooleServer;
use Thrift\Server\TServer;

class Server extends TServer
{
    public function serve()
    {
        $this->transport_->server->on('receive', [$this, 'handleReceive']);
        $this->transport_->listen();
    }

    public function stop()
    {
        $this->transport_->close();
    }

    /**
     * 处理RPC请求
     * @param Server $server
     * @param int $fd
     * @param int $fromId
     * @param string $data
     */
    public function handleReceive(SwooleServer $server, $fd, $fromId, $data)
    {
        $transport = new Transport($server, $fd, $data);
        $inputTransport = $this->inputTransportFactory_->getTransport($transport);
        $outputTransport = $this->outputTransportFactory_->getTransport($transport);
        $inputProtocol = $this->inputProtocolFactory_->getProtocol($inputTransport);
        $outputProtocol = $this->outputProtocolFactory_->getProtocol($outputTransport);
        $this->processor_->process($inputProtocol, $outputProtocol);
    }
}
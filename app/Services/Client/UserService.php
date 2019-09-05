<?php
namespace App\Services\Client;

use App\Swoole\ClientTransport;
use App\Thrift\User\UserClient;
use Thrift\Exception\TException;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

class UserService
{
    public function getUserInfoViaRpc(int $id)
    {
        try {
            // 建立与 RpcServer 的连接
            $socket = new TSocket("127.0.0.1", 8888);
            $socket->setRecvTimeout(30000);  // 超时时间
            $socket->setDebug(true);
            $transport = new TBufferedTransport($socket, 1024, 1024);
            $protocol = new TBinaryProtocol($transport);
            $thriftProtocol = new TMultiplexedProtocol($protocol, 'UserService');
            $client = new UserClient($thriftProtocol);
            $transport->open();
            $result = $client->getInfo($id);
            $transport->close();
            return $result;
        } catch (TException $TException) {
            dd($TException);
        }
    }

    public function getUserInfoViaSwoole(int $id)
    {
        try {
            // 建立与 SwooleServer 的连接
            $socket = new ClientTransport("127.0.0.1", 9999);
            $transport = new TFramedTransport($socket);
            $protocol = new TBinaryProtocol($transport);
            $client = new UserClient($protocol);
            $transport->open();
            $result = $client->getInfo($id);
            $transport->close();
            return $result;
        } catch (TException $TException) {
            dd($TException);
        }
    }
}
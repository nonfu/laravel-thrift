<?php
namespace App\Console\Commands;

use App\Services\Server\UserService;
use App\Thrift\User\UserProcessor;
use Illuminate\Console\Command;
use Thrift\Exception\TException;
use Thrift\Factory\TBinaryProtocolFactory;
use Thrift\Factory\TTransportFactory;
use Thrift\Server\TServerSocket;
use Thrift\Server\TSimpleServer;
use Thrift\TMultiplexedProcessor;

class RpcServerStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rpc:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Thrift RPC Server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $thriftProcess = new UserProcessor(new UserService());
            $tFactory = new TTransportFactory();
            $pFactory = new TBinaryProtocolFactory();
            $processor = new TMultiplexedProcessor();
            // 注册服务
            $processor->registerProcessor('UserService', $thriftProcess);
            // 监听本地 8888 端口，等待客户端连接请求
            $transport = new TServerSocket('127.0.0.1', 8888);
            $server = new TSimpleServer($processor, $transport, $tFactory, $tFactory, $pFactory, $pFactory);
            $this->info("服务启动成功[127.0.0.1:8888]！");
            $server->serve();
        } catch (TException $exception) {
            $this->error("服务启动失败！");
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Services\Server\UserService;
use App\Swoole\Server;
use App\Swoole\ServerTransport;
use App\Swoole\TFramedTransportFactory;
use App\Thrift\User\UserProcessor;
use Illuminate\Console\Command;
use Thrift\Exception\TException;
use Thrift\Factory\TBinaryProtocolFactory;

class SwooleServerStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Swoole Thrift RPC Server';

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
            $processor = new UserProcessor(new UserService());
            $tFactory = new TFramedTransportFactory();
            $pFactory = new TBinaryProtocolFactory();
            // 监听本地 9999 端口，等待客户端连接请求
            $transport = new ServerTransport('127.0.0.1', 9999);
            $server = new Server($processor, $transport, $tFactory, $tFactory, $pFactory, $pFactory);
            $this->info("服务监听地址: 127.0.0.1:9999");
            $server->serve();
        } catch (TException $exception) {
            $this->error("服务启动失败！");
        }
    }
}

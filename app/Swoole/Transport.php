<?php
namespace App\Swoole;

use Swoole\Server as SwooleServer;
use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;

class Transport extends TTransport
{
    /**
     * @var swoole服务器实例
     */
    protected $server;
    /**
     * @var int 客户端连接描述符
     */
    protected $fd = -1;
    /**
     * @var string 数据
     */
    protected $data = '';
    /**
     * @var int 数据读取指针
     */
    protected $offset = 0;
    
    /**
     * SwooleTransport constructor.
     * @param SwooleServer $server
     * @param int $fd
     * @param string $data
     */
    public function __construct(SwooleServer $server, $fd, $data)
    {
        $this->server = $server;
        $this->fd = $fd;
        $this->data = $data;
    }
    
    /**
     * Whether this transport is open.
     *
     * @return boolean true if open
     */
    public function isOpen()
    {
        return $this->fd > -1;
    }
    
    /**
     * Open the transport for reading/writing
     *
     * @throws TTransportException if cannot open
     */
    public function open()
    {
        if ($this->isOpen()) {
            throw new TTransportException('Swoole Transport already connected.', TTransportException::ALREADY_OPEN);
        }
    }
    
    /**
     * Close the transport.
     * @throws TTransportException
     */
    public function close()
    {
        if (!$this->isOpen()) {
            throw new TTransportException('Swoole Transport not open.', TTransportException::NOT_OPEN);
        }
        $this->server->close($this->fd, true);
        $this->fd = -1;
    }
    
    /**
     * Read some data into the array.
     *
     * @param int $len How much to read
     * @return string The data that has been read
     * @throws TTransportException if cannot read any more data
     */
    public function read($len)
    {
        if (strlen($this->data) - $this->offset < $len) {
            throw new TTransportException('Swoole Transport[' . strlen($this->data) . '] read ' . $len . ' bytes failed.');
        }
        $data = substr($this->data, $this->offset, $len);
        $this->offset += $len;
        return $data;
    }
   
    /**
     * Writes the given data out.
     *
     * @param string $buf The data to write
     * @throws TTransportException if writing fails
     */
    public function write($buf)
    {
        if (!$this->isOpen()) {
            throw new TTransportException('Swoole Transport not open.', TTransportException::NOT_OPEN);
        }
        $this->server->send($this->fd, $buf);
    }
}

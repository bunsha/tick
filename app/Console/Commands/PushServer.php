<?php

namespace App\Console\Commands;

use App\Classes\Socket\ChatSocket;
use App\Classes\Socket\Pusher;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use React\EventLoop\Factory as ReactLoop;
use React\ZMQ\Context as ReactContext;
use React\Socket\Server as Reactserver;

class PushServer extends Command
{
    protected $signature = 'push:start';
    protected $description = 'starting ChatSocket server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $loop   = ReactLoop::create();
        $pusher = new Pusher();
        $context = new ReactContext($loop);

        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', array($pusher, 'broadcast'));

        $webSock = new ReactServer($loop);
        $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
        $webServer = new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $pusher
                    )
                )
            ),
            $webSock
        );
        $this->info('Run handle');
        $loop->run();
    }
}

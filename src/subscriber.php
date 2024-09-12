<?php

// Create a new ZeroMQ context
$context = new ZMQContext();

// Create a new subscriber socket
$socket = $context->getSocket(ZMQ::SOCKET_SUB);
$socket->connect("tcp://localhost:5555");

// Subscribe to all messages
$socket->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");

// Receive messages
while (true) {
    $message = $socket->recv();
    echo "Received message: $message\n";
}

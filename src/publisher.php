<?php

echo 'hello';
// Create a new ZeroMQ context
$context = new ZMQContext();

// Create a new publisher socket
$socket = $context->getSocket(ZMQ::SOCKET_PUB);
$socket->bind("tcp://*:5555");

// Publish messages in a loop
while (true) {
    $socket->send("Hello Subscriber!");
    echo "Message sent!\n";
    sleep(3);
}

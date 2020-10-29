<?php
require __DIR__ . '/vendor/autoload.php';

$selectOptionStr = "Please select from the following options\n1. Get disk space (total on the Server) \n2. Get ping average to 8.8.8.8\n3. Request string from the user and print top 5 search results from Google\n";
$serverSingleton = \Kenshoo\ServerSingleton::getInstance();
$server = $serverSingleton->getServer();
$server->on('connection', function (\React\Socket\ConnectionInterface $client) use ($server, $selectOptionStr) {
    $client->write($selectOptionStr);

    // whenever a new message comes in
    $client->on('data', function ($data) use ($client, $server, $selectOptionStr) {
        // remove any non-word characters (just for the demo)
        $data = trim(preg_replace('/[^\w\d \.\,\-\!\?]/u', '', $data));

        // ignore empty messages
        if ($data === '') {
            return;
        }

        if ($data == '1') {
            $space = disk_total_space("/");
            $size = number_format($space / 1073741824, 2);
            $client->write("total disk space: $size GB\n");
            $client->write($selectOptionStr);
        }
        elseif ($data == '2') {
            $address = '8.8.8.8';
            $result = system("ping -c 1 $address" );
            $client->write($result . PHP_EOL);
            $client->write($selectOptionStr);
        }
        elseif ($data == '3') {
            $client->write("please enter string to search from google:\n");
            $client->on('data', function ($data) use ($client, $server, $selectOptionStr) {
                $googleItem = new \Kenshoo\GoogleItem();
                try {
                    $results =  $googleItem->getGoogleResult($data);
                    $client->write("5 results items\n" . print_r($results, true) . PHP_EOL);
                    $client->write($selectOptionStr);
                } catch (Exception $e) {
                    $client->write($e->getMessage());
                }
            });
        }
        elseif ($data == 'options') {
            $client->write($selectOptionStr);
        }
    });
});





$serverSingleton->run();



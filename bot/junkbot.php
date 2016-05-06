<?php
include "../config.php"; 
/*
 * This file is apart of the DiscordPHP project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */
use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\WebSockets\WebSocket;

// Includes the Composer autoload file
include __DIR__.'/../vendor/autoload.php';

/*if ($argc != 2) {
    echo 'You must pass your Token into the cmdline. Example: php basic.php <token>';
    die(1);
}*/


// Init the Discord instance.
//$discord = new Discord(['token' => $argv[1]]);
$discord = new Discord(['token' => MY_DISCORD_TOKEN]);

// Init the WebSocket instance.
$ws = new WebSocket($discord);

// We use EventEmitters to emit events. They are pretty much
// identical to the JavaScript/NodeJS implementation.
//
// Here we are waiting for the WebSocket client to parse the READY frame. Once
// it has done that it will run the code in the closure.
$ws->on(
    'ready',
    function ($discord) use ($ws) {
        // In here we can access any of the WebSocket events.
        //
        // There is a list of event constants that you can
        // find here: https://teamreflex.github.io/DiscordPHP/classes/Discord.WebSockets.Event.html
        //
        // We will echo to the console that the WebSocket is ready.
        echo 'Discord WebSocket is ready!'.PHP_EOL;

        $guild = $discord->guilds->first();
        $role = $guild->roles->first();

        $channel = $guild->channels->first();

        echo "Guild Name {$guild->name}  = Role Name: {$role->name}".PHP_EOL;

        echo $channel->name;

        foreach ($guild->channels as $ch) {
            echo "$ch->name | $ch->id\n";
        }

        //$channel_name = "testing";
        //$discord->guilds->get('id', $guild_id)->channels->get('name', $channel_name)->sendMessage("test msg");
        $guild->channels->get('name', BOT_CHANNEL_NAME)->sendMessage("Junk Bot Online!");


        // Here we will just log all messages.
        $ws->on(
            Event::MESSAGE_CREATE,
            function ($message, $discord, $newdiscord) {


                // We are just checking if the message equils to ping and replying to the user with a pong!
                if ($message->content == 'ping') {
                    $message->reply('pong!!!');
                }


                $reply = $message->timestamp->format('d/m/y H:i:s').' - '; // Format the message timestamp.
                if ($message->full_channel->is_private == 1) {
                    $reply .= "private - ";
                } else {
                    $reply .= $message->full_channel->guild->name.' - ';
                }
                $reply .= $message->author->username.' - '; // Add the message author's username onto the string.
                $reply .= $message->content; // Add the message content.
                echo $reply.PHP_EOL; // Finally, echo the message with a PHP end of line.


                if (strrpos($message->content, '/tester ') !== false) {
                    //$message->reply('Post to LFG');
                    $message->full_channel->guild->channels->get('name', "testing")->sendMessage("Slash Tester Fired");
                    $message->author->sendMessage("This is what you said");

                }
            }
        );


        /*$ws->on(
            Event::PRESENCE_UPDATE,
            function ($message, $discord, $newdiscord) {

                echo "UserID: $message->user\n";
                echo "game: $message->game\n";
                echo "status: $message->status\n";

                $uname = $message->user->username;

                echo "User - N: $uname\n";

                echo "PRESENCE_UPDATE".PHP_EOL; // Finally, echo the message with a PHP end of line.
            }
        );*/
    }
);

$ws->on(
    'error',
    function ($error, $ws) {
        dump($error);
        exit(1);
    }
);

// Now we will run the ReactPHP Event Loop!
$ws->run();

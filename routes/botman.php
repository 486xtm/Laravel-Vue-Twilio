<?php

$botman = app('botman');


//define all your bot commands
$botman->hears('foo', function($bot){
    $bot->reply('bar');
}); 

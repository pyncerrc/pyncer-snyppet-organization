<?php
use Pyncer\Snyppet\Snyppet;
use Pyncer\Snyppet\SnyppetManager;

SnyppetManager::register(new Snyppet(
    'organization',
    dirname(__DIR__),
    [
        'access' => ['Organization'],
    ],
));

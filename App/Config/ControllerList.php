<?php

$container['UserController'] = function ($container) use ($app)
{

    return new App\Controllers\UserController($container);

};
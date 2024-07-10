<?php

declare(strict_types=1);

$container['userService'] = static function (Pimple\Container $container): App\Service\UserService {
    return new App\Service\UserService($container['db']);
};

$container['postService'] = static function (Pimple\Container $container): App\Service\PostService {
    return new App\Service\PostService($container['db']);
};

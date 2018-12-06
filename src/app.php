<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use GuzzleHttp\Client as GuzzleClient;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());

//Configurations of Controllers and parameters
//Parameters
$app['base_uri'] = 'https://hacker-news.firebaseio.com/v0';
$app['items_per_page'] = 15;  //the number of items per page.
// Defining services for dependency injection
$app['hackernews.client'] = function ($app) {
    return new HackerNewsClient(new GuzzleClient(), $app['base_uri']);
};
$app['hackernews.itemmapper'] = function ($app) {
    return new HackerNewsItemMapperModel($app["hackernews.client"], $app['items_per_page']);
};
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    /**
     * @var TwigServiceProvider $twig
     */
    // add custom globals, filters, tags, ...
    $twig->addGlobal("itemsPerPage",$app['items_per_page']);
    $twig->addGlobal("menu", [
        "new" => "newest",
        "show" => "show",
        "ask" => "ask",
        "jobs" => "jobs"
    ]);

    return $twig;
});
return $app;

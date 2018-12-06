<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {


    //var_dump($app["hackernews.itemmapper"]->getArticleCommentsInATree("18600950", 2));

    $articles = $app["hackernews.itemmapper"]->getArticles("topstories", 1);
    //echo "done loading";
    //var_dump($app["hackernews.itemmapper"]->getArticles("topstories"));

    return $app['twig']->render('pages/articles.html.twig', [
        "articles" => $articles,
        "nextPageRoute" => "newest"
    ]);
})->bind('homepage')
;

$app->get('/newest', function(Request $request) use ($app){
    $page = $request->query->has("p") ? $request->query->get("p") : 1;

    $articles = $app["hackernews.itemmapper"]->getArticles("newstories", $page);
    //echo "done loading";
    //var_dump($app["hackernews.itemmapper"]->getArticles("topstories"));

    return $app['twig']->render('pages/articles.html.twig', [
        "articles" => $articles,
        "page" => $page
    ]);
})->bind("newest");

$app->get('/show', function(Request $request) use ($app){
    $page = $request->query->has("p") ? $request->query->get("p") : 1;

    $articles = $app["hackernews.itemmapper"]->getArticles("showstories", $page);

    return $app['twig']->render('pages/articles.html.twig', [
        "articles" => $articles,
        "page" => $page
    ]);
})->bind("show");

$app->get('/ask', function(Request $request) use ($app){
    $page = $request->query->has("p") ? $request->query->get("p") : 1;

    $articles = $app["hackernews.itemmapper"]->getArticles("askstories", $page);

    return $app['twig']->render('pages/articles.html.twig', [
        "articles" => $articles,
        "page" => $page
    ]);
})->bind("ask");

$app->get('/jobs', function(Request $request) use ($app){
    $page = $request->query->has("p") ? $request->query->get("p") : 1;

    $articles = $app["hackernews.itemmapper"]->getArticles("jobstories", $page);

    return $app['twig']->render('pages/articles.html.twig', [
        "articles" => $articles,
        "page" => $page
    ]);
})->bind("jobs");

$app->get('/item/{id}', function(Request $request, $id) use ($app){
    $page = $request->query->has("p") ? $request->query->get("p") : 1;


    $comments = $app["hackernews.itemmapper"]->getArticleCommentsInATree($id, $page);

    return $app['twig']->render('pages/comments.html.twig', [
        "comments" => $comments,
        "page" => $page
    ]);
})->bind("comments");

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

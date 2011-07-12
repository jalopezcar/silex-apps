<?php

require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

$app->register(new Silex\Extension\UrlGeneratorExtension());
$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options'            => array(
        'driver'    => 'pdo_sqlite',
        'path'      => __DIR__.'/app.db',
    ),
    'db.dbal.class_path'    => __DIR__.'/vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/vendor/doctrine-common/lib',
));


$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/twig/lib',
));


$app->get('/', function () use ($app) {
    $sql = "SELECT * FROM project p INNER JOIN module m ON m.project_id = p.id";
    $apps = $app['db']->executeQuery($sql)->fetchAll();
print_r($apps);
    return $app['twig']->render('app.html.twig', array(
        'apps' => $apps,
    ));

});


$app->get('/add/{option}', function ($option) use ($app) {    

    $form = array();
    $form['name']='';
    $form['trac']='http://dev.map.es/trac/';
    $form['url']='http://dev.map.es/trac/';
     
    if($option){
    die('save');
    }
    return $app['twig']->render('add.html.twig',array('form'=>$form));
})->bind('demo');

$app->get('/saveProyect', function () use ($app) {    
    return $app['twig']->render('add.html.twig');
});

$app->run();


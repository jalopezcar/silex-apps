<?php

require_once __DIR__.'/silex.phar';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

$app = new Silex\Application();

$app['autoloader']->registerNamespace('Symfony', __DIR__.'/vendor/symfony/src');
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



$app->get('/favorite/{user}/{action}/{app_name}/{favorites}', function ($user,$action,$app_name,$favorites) use ($app) {
	
	try{
		$apps_user = Yaml::parse('config/'.$user.'.yml');
	} catch (\InvalidArgumentException $e) {
	    // an error occurred during parsing
		return new Response("Unable to parse the YAML string: ".$e->getMessage(),500);
	}
	print_r($apps_user);
	echo $app_name,$action;
	$apps_user[ $app_name ]['favorite']= ($action=='add') ? true : false;
	print_r($apps_user);
	
	//$dumper = new Dumper();
	$yaml = Yaml::dump($apps_user);
	file_put_contents('config/'.$user.'.yml', $yaml);
	
	return $app->redirect( '/index.php/'.$user.'/'.$favorites );
	
})->value('favorites','');


$app->get('/{user}/{favorites}', function ($user,$favorites) use ($app) {

	try {
	    $apps = Yaml::parse('config/apps.yml');
	} catch (\InvalidArgumentException $e) {
	    // an error occurred during parsing
		return new Response("Unable to parse the YAML string: ".$e->getMessage(),500);
	}
	
	if($user){
		try{
			$apps_user = Yaml::parse('config/'.$user.'.yml');
		} catch (\InvalidArgumentException $e) {
	    	// an error occurred during parsing
			return new Response("Unable to parse the YAML string: ".$e->getMessage(),500);
		}
		$apps = array_merge_recursive($apps_user,$apps);
	}
	//print_r($apps);
    return $app['twig']->render('app.html.twig', array(
        'apps' => $apps,
		'favorites' => $favorites,
		'user' => $user
    ));

})->value('user', '')->value('favorites','');



$app->run();


<?php

define('PAGE_DIR', __DIR__.'/pages');

$router_config = array();

#exception pages
$router_config['__default__']	= array('__page__'=>'home.phtml');
$router_config['__undefine__']	= array('__page__'=>'404.phtml');

#your pages
$router_config['home']			= array('__page__'=>'home.phtml');
$router_config['element']		= array('__page__'=>'element.phtml');
$router_config['generic']		= array('__page__'=>'generic.phtml');

#$router_config['support']['multiple']['tier'] = array('__page__'=>'multile_tier.phtml');




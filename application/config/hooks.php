<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


$hook['post_controller_constructor'][] = [
    'class' => 'TemplateHooks',
    'function' => 'preloadData',
    'filename' => 'TemplateHooks.php',
    'filepath' => 'hooks',
    'params' => [],
];

$hook['pre_system'][] = function() {
    $project_root = APPPATH.'../';

    try {
        if(is_dir($project_root) && file_exists($project_root . '.env')){
            $dotenv = Dotenv\Dotenv::createImmutable($project_root);
            $dotenv->load();
        }
    } catch(Exception $exception){
        $error =  '.env file missing';
        log_message('error',$error);
        die($error);
    }

};
$hook['pre_system'][] = [
    'class' => 'PageLoadHooks',
    'function' => 'registerStart',
    'filename' => 'PageLoadHooks.php',
    'filepath' => 'hooks',
    'params' => [],
];


$hook['post_controller'][] = [
    'class' => 'PageLoadHooks',
    'function' => 'recordDuration',
    'filename' => 'PageLoadHooks.php',
    'filepath' => 'hooks',
    'params' => [],
];
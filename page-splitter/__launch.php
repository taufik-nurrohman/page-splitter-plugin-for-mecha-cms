<?php


/**
 * Editor Toolbar
 * --------------
 */

$posts = Mecha::walk(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR), function($v) {
    return File::B($v);
});

$route__ = $config->manager->slug . '/' . implode('|', $posts) . '/';

Config::merge('DASHBOARD.languages.MTE.plugin_page_splitter.title.split', $speak->plugin_page_splitter->title->split);

if(Route::is($route__ . 'ignite') || Route::is($route__ . 'repair/id:(:num)')) {
    Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
        echo Asset::javascript(__DIR__ . DS . 'assets' . DS . 'sword' . DS . 'button.js', "", 'sword/editor.button.' . File::B(__DIR__) . '.min.js');
    }, 20);
}


/**
 * Plugin Updater
 * --------------
 */

Route::over($config->manager->slug . '/plugin/' . File::B(__DIR__) . '/update', function() {
    File::write(Request::post('css'))->saveTo(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css');
    $_POST['query'] = Text::parse($_POST['query'], '->array_key');
    unset($_POST['css']);
});
<?php


/**
 * Editor Toolbar
 * --------------
 */

$route__ = $config->manager->slug . '/(article|page)/';

Config::merge('DASHBOARD.languages.MTE', array(
    'plugin_page_splitter_title_split' => $speak->plugin_page_splitter_title_split
));

if(Route::is($route__ . 'ignite') || Route::is($route__ . 'repair/id:(:num)')) {
    Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
        echo Asset::javascript(__DIR__ . DS . 'assets' . DS . 'sword' . DS . 'button.js');
    }, 20);
}


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/' . File::B(__DIR__) . '/update', function() use($config, $speak) {
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        File::write($request['css'])->saveTo(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css');
        unset($request['token']); // Remove token from request array
        unset($request['css']); // Remove CSS from request array
        $request['query'] = Text::parse($request['query'], '->array_key');
        File::serialize($request)->saveTo(__DIR__ . DS . 'states' . DS . 'config.txt', 0600);
        Notify::success(Config::speak('notify_success_updated', $speak->plugin));
        Guardian::kick(File::D($config->url_current));
    }
});
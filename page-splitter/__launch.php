<?php

// Add quick toolbar icon to the post editor
Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
    echo '<script>
(function(base) {
    if(typeof base.composer === "undefined") return;
    base.composer.button(\'files-o\', {
        \'title\': \'' . Config::speak('ps_plugin_title_split') . '\',
        \'click\': function(e, editor) {
            editor.grip.insert(\'\\n\\n<!-- next -->\\n\\n\');
        },
        \'position\': 12
    });
})(DASHBOARD);
</script>';
}, 20);


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/' . basename(__DIR__) . '/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        File::write($request['css'])->saveTo(PLUGIN . DS . basename(__DIR__) . DS . 'shell' . DS . 'ps.css');
        unset($request['token']); // Remove token from request array
        unset($request['css']); // Remove CSS from request array
        $request['query'] = Text::parse($request['query'], '->array_key');
        File::serialize($request)->saveTo(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt', 0600);
        Notify::success(Config::speak('notify_success_updated', $speak->plugin));
        Guardian::kick(dirname($config->url_current));
    }
});
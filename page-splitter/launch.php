<?php

// Load the configuration file
$ps_config = File::open(PLUGIN . DS . 'page-splitter' . DS . 'states' . DS . 'config.txt')->unserialize();

// Include the Page Splitter's CSS
Weapon::add('shell_after', function() {
    echo Asset::stylesheet('cabinet/plugins/page-splitter/shell/ps.css');
});

// The page splitter function
function do_split_page_content($content) {

    global $config, $speak, $ps_config;
 
    // Do nothing if the minimum required pattern not found in page content
    if(strpos($content, '<!--') === false) return $content;

    // Add a closing pattern if the closing pattern not found
    if(strpos($content, '<!-- end:steps -->') === false) {
        $content .= "\n\n" . '<!-- end:steps -->';
    }

    // Paginate the whole page content if the opening and closing pattern not found
    if(strpos($content, '<!-- begin:steps -->') === false) {
        $content = '<!-- begin:steps -->' . "\n\n" . $content . "\n\n" . '<!-- end:steps -->';
    }

    // Parse the page content
    $content = preg_replace_callback('#<\!\-\- begin\:steps \-\->([\s\S]+)<\!\-\- end\:steps \-\->#m', function($matches) use($config, $speak, $ps_config) {

        // Create the page content parts
        $parts = explode('<!-- next -->', trim($matches[1]));

        // Define page offset
        $offset = (int) Request::get($ps_config['query'], 1);

        // Build the pagination
        $pager = Navigator::extract($parts, $offset, 1, str_replace($config->url . '/', "", $config->url_current));
        $pager_next_prev = ( ! empty($pager->prev->link) ? '<a class="ps-pager-prev" href="' . $pager->prev->url . '">' . $speak->prev . '</a>' : '<span class="ps-pager-prev">' . $speak->prev . '</span>') . ( ! empty($pager->next->link) ? '<a class="ps-pager-next" href="' . $pager->next->url . '">' . $speak->next . '</a>' : '<span class="ps-pager-next">' . $speak->next . '</span>');
        $pagination = $ps_config['pagination'] == 'step' ? $pager->step->link : $pager_next_prev;

        // Convert `foo/bar/1` into `foo/bar?step=1`
        $pagination = preg_replace('#' . $config->index->slug . '/(.*?)/([0-9]+)"#', $config->index->slug . '/$1?' . $ps_config['query'] . '=$2"', $pagination);

        // Output the results
        return '<div class="ps-area cl cf p"><div class="ps-step-' . $offset . '">' . (isset($parts[$offset - 1]) ? $parts[$offset - 1] : $speak->notify_error_not_found) . '</div><nav class="ps-pager cl cf p">' . $pagination . '</nav></div>';

    }, $content);

    // Output the results
    return $content;

}

// Apply filters
Filter::add('article:content', 'do_split_page_content', 40);
Filter::add('page:content', 'do_split_page_content', 40);

// Add quick toolbar icon to the post editor
if(preg_match('#' . $config->manager->slug . '\/(article|page)\/(ignite|repair)#', $config->url_current)) {
    Weapon::add('SHIPMENT_REGION_BOTTOM', function() {
        echo '<script>
(function(base, $) {
    base.composer.button(\'files-o\', {
        \'title\': \'' . Config::speak('ps_plugin_title_split') . '\',
        \'click\': function(e, editor) {
            editor.grip.insert(\'\\n\\n<!-- next -->\\n\\n\');
        },
        \'position\': 12
    });
})(DASHBOARD, Zepto);
</script>';
    }, 20);
}


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/page-splitter/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        File::write($request['css'])->saveTo(PLUGIN . DS . 'page-splitter' . DS . 'shell' . DS . 'ps.css');
        unset($request['token']); // Remove token from request array
        unset($request['css']); // Remove CSS from request array
        $request['query'] = Text::parse($request['query'])->to_array_key;
        File::serialize($request)->saveTo(PLUGIN . DS . 'page-splitter' . DS . 'states' . DS . 'config.txt');
        Notify::success(Config::speak('notify_success_updated', array($speak->plugin)));
        Guardian::kick(dirname($config->url_current));
    }
});
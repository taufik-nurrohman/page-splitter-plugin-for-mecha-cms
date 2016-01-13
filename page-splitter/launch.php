<?php

// Load the configuration file
$page_splitter_config = File::open(__DIR__ . DS . 'states' . DS . 'config.txt')->unserialize();

// The page splitter function
function do_page_splitter($content) {
    global $config, $speak, $page_splitter_config; 
    // Do nothing if the minimum required pattern wasn't found in page content
    if(strpos($content, '<!-- next -->') === false) return $content;
    // Add a closing pattern if the closing pattern wasn't found
    if(strpos($content, '<!-- end:step -->') === false) {
        $content .= "\n\n" . '<!-- end:step -->';
    }
    // Paginate the whole page content if the opening and closing pattern wasn't found
    if(strpos($content, '<!-- begin:step -->') === false) {
        $content = '<!-- begin:step -->' . "\n\n" . $content . "\n\n" . '<!-- end:step -->';
    }
    // Parse the page content
    $content = preg_replace_callback('#<\!\-\- begin\:steps? \-\->([\s\S]+)<\!\-\- end\:steps? \-\->#', function($matches) use($config, $speak, $page_splitter_config) {
        // Create the page content part(s)
        $parts = explode('<!-- next -->', trim($matches[1]));
        // Define page offset
        $offset = Request::get($page_splitter_config['query'], 1);
        // Remove query string duplicate
        $config->url_query = HTTP::query($page_splitter_config['query'], false);
        Config::set('url_query', $config->url_query);
        // Generate query string URL-based pagination link(s)
        $pager = Navigator::extract($parts, $offset, 1, '/' . $config->url_path . '?' . $page_splitter_config['query'] . '=%s');
        $pager_next_prev = ( ! empty($pager->prev->anchor) ? '<a class="ps-pager-prev" href="' . $pager->prev->url . '">' . $speak->prev . '</a>' : '<span class="ps-pager-prev">' . $speak->prev . '</span>') . ( ! empty($pager->next->anchor) ? '<a class="ps-pager-next" href="' . $pager->next->url . '">' . $speak->next . '</a>' : '<span class="ps-pager-next">' . $speak->next . '</span>');
        $pager = $page_splitter_config['pager'] === 2 ? $pager->step->html : $pager_next_prev;
        // Output the result(s)
        $output = isset($parts[$offset - 1]) ? $parts[$offset - 1] : $speak->notify_error_not_found;
        unset($parts);
        return '<div class="ps-area cl cf p"><div class="ps-step ps-step-' . $offset . '" id="ps-step-' . $offset . '">' . $output . '</div><nav class="ps-pager cl cf p">' . $pager . '</nav></div>';
    }, $content);
    // Output the result(s)
    return $content;
}

if($config->is->post) {
    // Register the `do_page_splitter` filter ...
    Filter::add($config->page_type . ':content', 'do_page_splitter', 9); // filter stack value should be less than the TOC plugin's filter stack value
    // Include the Page Splitter's CSS
    Weapon::add('shell_after', function() {
        echo Asset::stylesheet(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css');
    });
}
<?php

// Load the configuration file
$c_page_splitter = $config->states->{'plugin_' . md5(File::B(__DIR__))};

// The page splitter function
function do_page_splitter($content) {
    global $config, $speak, $c_page_splitter; 
    // Do nothing if the minimum required pattern wasn't found in page content
    if(strpos($content, '<!-- next -->') === false) return $content;
    // Add a closing pattern if the closing pattern wasn't found
    if(strpos($content, '<!-- end:step -->') === false) {
        $content .= "\n\n" . '<!-- end:step -->';
    }
    // Paginate the whole page content if the opening and closing pattern wasn't found
    if(strpos($content, '<!-- begin:step -->') === false) {
        $content = '<!-- begin:step -->' . "\n\n" . $content;
    }
    // Parse the page content
    $content = preg_replace_callback('#<\!\-\- begin\:steps? \-\->([\s\S]+?)<\!\-\- end\:steps? \-\->#', function($matches) use($config, $speak, $c_page_splitter) {
        // Split the page content part(s)
        $parts = explode('<!-- next -->', trim($matches[1]));
        // Define page offset
        $offset = Request::get($c_page_splitter->query, 1);
        // Remove query string duplicate
        $config->url_query = HTTP::query($c_page_splitter->query, false);
        Config::set('url_query', $config->url_query);
        // Generate query string URL-based pagination link(s)
        $pager = Navigator::extract($parts, $offset, 1, '/' . $config->url_path . '?' . $c_page_splitter->query . '=%s');
        $pager_next_prev = ( ! empty($pager->prev->anchor) ? '<a class="page-splitter-pager-prev" href="' . $pager->prev->url . '">' . $speak->prev . '</a>' : '<span class="page-splitter-pager-prev">' . $speak->prev . '</span>') . ( ! empty($pager->next->anchor) ? '<a class="page-splitter-pager-next" href="' . $pager->next->url . '">' . $speak->next . '</a>' : '<span class="page-splitter-pager-next">' . $speak->next . '</span>');
        $pager = $c_page_splitter->pager === 2 ? $pager->step->html : $pager_next_prev;
        // Output the result(s)
        $output = isset($parts[$offset - 1]) ? $parts[$offset - 1] : $speak->notify_error_not_found;
        unset($parts);
        return '<div class="page-splitter-area cl cf p"><div class="page-splitter-step page-splitter-step-' . $offset . '" id="page-splitter-step-' . $offset . '">' . $output . '</div><nav class="page-splitter-pager cl cf p">' . $pager . '</nav></div>';
    }, $content);
    // Output the result(s)
    return $content;
}

if($config->is->post) {
    // Register the `do_page_splitter` filter ...
    Filter::add($config->page_type . ':content', 'do_page_splitter', 9); // filter stack value should be less than the TOC plugin's filter stack value
    // Include the Page Splitter's CSS
    Weapon::add('shell_after', function() {
        echo Asset::stylesheet(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css', "", 'shell/page-splitter.min.css');
    });
}
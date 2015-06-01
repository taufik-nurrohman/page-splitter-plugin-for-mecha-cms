<?php

// Load the configuration file
$ps_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize();

// Include the Page Splitter's CSS
Weapon::add('shell_after', function() {
    echo Asset::stylesheet('cabinet/plugins/' . basename(__DIR__) . '/shell/ps.css');
});

// The page splitter function
function do_split_page_content($content) {
    global $config, $speak, $ps_config; 
    // Do nothing if the minimum required pattern not found in page content
    if(strpos($content, '<!-- next -->') === false) return $content;
    // Add a closing pattern if the closing pattern not found
    if(strpos($content, '<!-- end:steps -->') === false) {
        $content .= "\n\n" . '<!-- end:steps -->';
    }
    // Paginate the whole page content if the opening and closing pattern not found
    if(strpos($content, '<!-- begin:steps -->') === false) {
        $content = '<!-- begin:steps -->' . "\n\n" . $content . "\n\n" . '<!-- end:steps -->';
    }
    // Parse the page content
    $content = preg_replace_callback('#<\!\-\- begin\:steps \-\->([\s\S]+)<\!\-\- end\:steps \-\->#', function($matches) use($config, $speak, $ps_config) {
        // Create the page content parts
        $parts = explode('<!-- next -->', trim($matches[1]));
        // Define page offset
        $offset = Request::get($ps_config['query'], 1);
        // Cache the query string URL data
        $q_old = $config->url_query;
        // Remove the query string URL data from registry
        Config::set('url_query', "");
        // Generate clean pagination markup without query string URL
        $pager = Navigator::extract($parts, $offset, 1, $config->url_path);
        // Put back the old query string URL data
        $config->url_query = $q_old;
        Config::set('url_query', $q_old);
        unset($q_old);
        $pager_next_prev = ( ! empty($pager->prev->link) ? '<a class="ps-pager-prev" href="' . $pager->prev->url . '">' . $speak->prev . '</a>' : '<span class="ps-pager-prev">' . $speak->prev . '</span>') . ( ! empty($pager->next->link) ? '<a class="ps-pager-next" href="' . $pager->next->url . '">' . $speak->next . '</a>' : '<span class="ps-pager-next">' . $speak->next . '</span>');
        $pagination = $ps_config['pagination'] === 'step' ? $pager->step->link : $pager_next_prev;
        // Re-build query string URL
        $q = "";
        if(isset($_GET) && is_array($_GET)) {
            unset($_GET[$ps_config['query']]); // Remove duplicate
            foreach($_GET as $k => $v) {
                $q .= '&amp;' . $k . '=' . $v;
            }
        }
        // Convert `foo/bar/1` to `foo/bar?step=1`
        $pagination = preg_replace('#' . $config->index->slug . '/(.*?)/(\d+)([\'"])#', $config->index->slug . '/$1?' . $ps_config['query'] . '=$2' . $q . '$3', $pagination);
        // Output the results
        $output = isset($parts[$offset - 1]) ? $parts[$offset - 1] : $speak->notify_error_not_found;
        unset($parts);
        return '<div class="ps-area cl cf p"><div class="ps-step-' . $offset . '">' . $output . '</div><nav class="ps-pager cl cf p">' . $pagination . '</nav></div>';
    }, $content);
    // Output the results
    return $content;
}

// Register the filters
Filter::add('article:content', 'do_split_page_content', 40);
Filter::add('page:content', 'do_split_page_content', 40);
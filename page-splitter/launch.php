<?php

// Load the configuration file
$ps_config = File::open(PLUGIN . DS . File::B(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize();

// Include the Page Splitter's CSS
Weapon::add('shell_after', function() {
    echo Asset::stylesheet('cabinet/plugins/' . File::B(__DIR__) . '/assets/shell/ps.css');
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
        // Remove `?step=%s` query string URL duplicate
        unset($_GET[$ps_config['query']]);
        // Re-build query string URL
        $q = array();
        foreach($_GET as $k => $v) {
            $q[] = $k . '=' . Text::parse($v, '->encoded_url');
        }
        $config->url_query = ! empty($q) ? '?' . implode('&', $q) : "";
        Config::set('url_query', $config->url_query);
        unset($q);
        // Generate query string URL-based pagination links
        $pager = Navigator::extract($parts, $offset, 1, '/' . $config->url_path . '?' . $ps_config['query'] . '=%s');
        $pager_next_prev = ( ! empty($pager->prev->link) ? '<a class="ps-pager-prev" href="' . $pager->prev->url . '">' . $speak->prev . '</a>' : '<span class="ps-pager-prev">' . $speak->prev . '</span>') . ( ! empty($pager->next->link) ? '<a class="ps-pager-next" href="' . $pager->next->url . '">' . $speak->next . '</a>' : '<span class="ps-pager-next">' . $speak->next . '</span>');
        $pagination = $ps_config['pagination'] === 'step' ? $pager->step->link : $pager_next_prev;
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
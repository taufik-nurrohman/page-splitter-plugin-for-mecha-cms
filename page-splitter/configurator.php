<form class="form-plugin" action="<?php echo $config->url_current; ?>/update" method="post">
  <?php $page_splitter_config = File::open(__DIR__ . DS . 'states' . DS . 'config.txt')->unserialize(); $page_splitter_css = File::open(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css')->read(); ?>
  <?php echo Form::hidden('token', $token); ?>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_query; ?></span>
    <span class="grid span-5"><?php echo Form::text('query', $page_splitter_config['query'], null, array('class' => 'input-block')); ?></span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_pagination; ?></span>
    <span class="grid span-5">
    <?php

    $s = (array) $speak->plugin_page_splitter_title_pager;
    $options = array(
        1 => $s[1],
        2 => $s[2]
    );

    echo Form::select('pager', $options, $page_splitter_config['pager'], array('class' => 'select-block'));

    ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_css; ?></span>
    <span class="grid span-5"><?php echo Form::textarea('css', $page_splitter_css, null, array('class' => array('textarea-block', 'textarea-expand', 'code'))); ?></span>
  </label>
  <div class="grid-group">
    <span class="grid span-1"></span>
    <span class="grid span-5"><?php echo Jot::button('action', $speak->update); ?></span>
  </div>
</form>
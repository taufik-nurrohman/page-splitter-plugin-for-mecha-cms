<form class="form-plugin" action="<?php echo $config->url_current; ?>/update" method="post">
  <?php $ps_config = File::open(PLUGIN . DS . File::B(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); $ps_css = File::open(PLUGIN . DS . File::B(__DIR__) . DS . 'assets' . DS . 'shell' . DS . 'ps.css')->read(); ?>
  <?php echo Form::hidden('token', $token); ?>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_query; ?></span>
    <span class="grid span-5"><?php echo Form::text('query', $ps_config['query'], null, array('class' => 'input-block')); ?></span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_pagination; ?></span>
    <span class="grid span-5">
    <?php

    $options = array(
        'next/prev' => $speak->plugin_page_splitter_title_pagination_next_prev,
        'step' => $speak->plugin_page_splitter_title_pagination_step
    );

    echo Form::select('pagination', $options, $ps_config['pagination'], array('class' => 'select-block'));

    ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter_title_appearance; ?></span>
    <span class="grid span-5"><?php echo Form::textarea('css', $ps_css, null, array('class' => array('textarea-block', 'textarea-expand', 'code'))); ?></span>
  </label>
  <div class="grid-group">
    <span class="grid span-1"></span>
    <span class="grid span-5"><?php echo Jot::button('action', $speak->update); ?></span>
  </div>
</form>
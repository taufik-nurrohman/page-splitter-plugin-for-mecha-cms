<?php

$c_page_splitter = $config->states->{'plugin_' . md5(File::B(__DIR__))};
$c_page_splitter_css = File::open(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'page-splitter.css')->read();

?>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter->title->query; ?></span>
  <span class="grid span-5"><?php echo Form::text('query', $c_page_splitter->query, null, array('class' => 'input-block')); ?></span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter->title->pagination; ?></span>
  <span class="grid span-5">
  <?php

  $s = (array) $speak->plugin_page_splitter->title->pager;
  $options = array(
      1 => $s[1],
      2 => $s[2]
  );

  echo Form::select('pager', $options, $c_page_splitter->pager, array('class' => 'select-block'));

  ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_page_splitter->title->css; ?></span>
  <span class="grid span-5"><?php echo Form::textarea('css', $c_page_splitter_css, null, array('class' => array('textarea-block', 'textarea-expand', 'code'))); ?></span>
</label>
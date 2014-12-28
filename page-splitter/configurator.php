<form class="form-plugin" action="<?php echo $config->url_current; ?>/update" method="post">
  <input name="token" type="hidden" value="<?php $ps_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); $ps_css = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'shell' . DS . 'ps.css')->read(); echo $token; ?>">
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->ps_plugin_title_query; ?></span>
    <span class="grid span-5"><input name="query" type="text" class="input-block" value="<?php echo Guardian::wayback('query', $ps_config['query']); ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->ps_plugin_title_pagination; ?></span>
    <span class="grid span-5">
      <select name="pagination" class="select-block">
      <?php

      $options = array(
          'next/prev' => $speak->ps_plugin_title_pagination_next_prev,
          'step' => $speak->ps_plugin_title_pagination_step
      );

      foreach($options as $k => $v) {
          echo '<option value="' . $k . '"' . (Guardian::wayback('pagination', $ps_config['pagination']) == $k ? ' selected' : "") . '>' . $v . '</option>';
      }

      ?>
      </select>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->ps_plugin_title_appearance; ?></span>
    <span class="grid span-5"><textarea name="css" class="textarea-block code"><?php echo Text::parse(Guardian::wayback('css', $ps_css))->to_encoded_html; ?></textarea></span>
  </label>
  <div class="grid-group">
    <span class="grid span-1"></span>
    <span class="grid span-5"><button class="btn btn-action" type="submit"><i class="fa fa-check-circle"></i> <?php echo $speak->update; ?></button></span>
  </div>
</form>
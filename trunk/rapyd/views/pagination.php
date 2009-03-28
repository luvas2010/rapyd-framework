
<?php if ($first_page): ?>
  <a href="<?php echo str_replace(rawurlencode('{page}'), 1, $url) ?>">&lsaquo;&nbsp;<?php echo rpd::lang('pag.first')?></a>
<?php endif ?>
<?php if ($previous_page): ?>
  <a href="<?php echo str_replace(rawurlencode('{page}'), $previous_page, $url) ?>">&lt;</a>
<?php endif ?>
<?php for ($i = 1; $i <= $total_pages; $i++): ?>
  <?php if ($i == $current_page): ?>
    <strong><?php echo $i ?></strong>
  <?php else: ?>
    <a href="<?php echo str_replace(rawurlencode('{page}'), $i, $url) ?>"><?php echo $i ?></a>
  <?php endif ?>
<?php endfor ?>
<?php if ($next_page): ?>
  <a href="<?php echo str_replace(rawurlencode('{page}'), $next_page, $url) ?>">&gt;</a>
<?php endif ?>
<?php if ($last_page): ?>
  <a href="<?php echo str_replace(rawurlencode('{page}'), $last_page, $url) ?>"><?php echo rpd::lang('pag.last')?> &nbsp;&rsaquo;</a>
<?php endif ?>

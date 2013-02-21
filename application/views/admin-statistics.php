<?php $this->load->view('common/header'); ?>
<script src="/js/raphael.js"></script>
<script src="/js/popup.js"></script>
<script src="/js/analytics.js"></script>

<table id="data" style="display: none;">
  <thead>
    <tr>
      <?php foreach($statistics as $s) { ?>
        <th><?php echo $s->y.'-'.$s->m.($s->d > 0 ? '-'.$s->d : '') ?></th> 
      <?php } ?>
    </tr>
  </thead>
  <tfoot> 
    <tr>
      <?php $i = 0; foreach($statistics as $s) { $i++; ?>
        <th><?php echo ($i % 4) == 2 || $s->d == 0 ? ($s->d > 0 ? date("M jS", strtotime($s->y."-".$s->m."-".$s->d)) : date("M", strtotime($s->y."-".$s->m))) : NULL ?></th>
      <?php } ?>
    </tr> 
  </tfoot> 
  <tbody> 
    <tr> 
      <?php foreach($statistics as $s) { ?>
      <td><?php echo $s->v ?></td>
      <?php } ?>
    </tr> 
  </tbody> 
</table>

<div id="holder" class="form-awesome form-awesome-wider round-10" style="padding:10px 20px;text-shadow:none"></div>

<form class="form-awesome form-awesome-wider round-10" action="#" method="get" onsubmit="return false">
  <ul>
    <li>
      <label for="table">Metric:</label>
      <select id="table">
        <option value="users" <?php if ($table == "users") { echo "selected"; } ?>>Users</option>
        <option value="posts" <?php if ($table == "posts") { echo "selected"; } ?>>Posts</option>
        <option value="uploads"<?php if ($table == "uploads") { echo "selected"; } ?>>Uploads</option>
        <option value="pms"<?php if ($table == "pms") { echo "selected"; } ?>>PMs</option>
        <option value="badges_box"<?php if ($table == "badges_box") { echo "selected"; } ?>>Badges</option>
        <option value="posts_votes"<?php if ($table == "posts_votes") { echo "selected"; } ?>>Votes</option>
      </select>
    </li>
    <li>
      <label for="granularity">Zoom:</label>
      <select id="granularity">
        <option value="monthly" <?php if ($granularity == "monthly") { echo "selected"; } ?>>Monthly</option>
        <option value="daily" <?php if ($granularity == "daily") { echo "selected"; } ?>>Daily</option>
      </select>
    </li>
    <li>
      <label for="start">Start date:</label>
      <input type="text" id="start" value="<?php echo $start; ?>">
    </li>
    <li>
      <label for="end">End date:</label>
      <input type="text" id="end" value="<?php echo $end; ?>">
    </li>
    <li class="right">
      <button onclick='javascript:window.location = "/admin/statistics/" + document.getElementById("table").value + "/" + document.getElementById("granularity").value + "/" + document.getElementById("start").value + "/" + document.getElementById("end").value;'><span>Go</span></button>
    </li>
  </ul>
</form>

<?php $this->load->view('common/footer'); ?>
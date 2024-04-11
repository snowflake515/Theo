<div class="page-header">
  <h1>
    Edit appointment 
  </h1>
</div>
<form class="form-horizontal" role="form" method="post" action="<?php echo site_url('schedule/appointment_update') ?>" accept-charset="UTF-8">
  <input type="hidden" value="<?php echo $dt->Appointments_ID ?>" name="Appointments_ID">
  <?php $this->load->view('schedule/appointment_form'); ?>
  <div class="form-group">
    <br/>
    <div class="col-sm-offset-4 col-sm-8">
      <?php
      echo '<button type="submit" class="btn btn-default" value="back" name="back"><i class="icon icon-arrow-left"></i>&nbsp;&nbsp; Back</button>';
      ?>
      <button type = "submit" class = "btn btn-primary"><i class = "icon icon-save"></i>&nbsp;&nbsp;Save</button>
      <a href = "<?php echo site_url('schedule/appointment_destroy/' . $dt->Appointments_ID) ?>" class = "btn btn-danger need_confrim"><i class = "icon icon-trash"></i>&nbsp;&nbsp;Delete</a>
      <?php echo anchor('encounter/start/' . $dt->Appointments_ID, '<i class="icon icon-time"></i> &nbsp;Start Encounter', array('class' => 'btn btn-success'));
      ?> 
    </div>
  </div>
</form>

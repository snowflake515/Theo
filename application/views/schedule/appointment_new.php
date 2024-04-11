<div class="page-header">
  <h1>
    Add appointment
  </h1>
</div>
<form class="form-horizontal" role="form" method="post" action="<?php echo site_url('schedule/appointment_create') ?>" accept-charset="UTF-8">
  <?php $this->load->view('schedule/appointment_form'); ?>
  <br/>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
      <?php echo anchor('schedule', '<i class="icon icon-arrow-left"></i>&nbsp;&nbsp;  Back', array('class' => 'btn btn-default')); ?>
      <button type="submit" class="btn btn-primary" <?php echo disabled(form_value('Patient_ID', $patient)) ?>><i class="icon icon-save"></i>&nbsp;&nbsp; Save</button>
    </div>
  </div>
</form>

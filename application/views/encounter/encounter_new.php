<div class="page-header">
  <h1>
    Encounters
  </h1>
</div>

  <form class="form-horizontal" role="form" method="post" action="<?php echo site_url('encounter/encounter_create') ?>" accept-charset="UTF-8">
    <?php $this->load->view('encounter/encounter_form'); ?>
    <div class="form-group">
      <div class="col-sm-offset-4 col-sm-8">
        <button type="submit" class="btn btn-primary"><i class="icon icon-save"></i>&nbsp;&nbsp;  Save</button>
        <?php echo anchor('template/tml1/'.$dt->Encounter_ID.'/'.$dt->Appointments_ID,'<i class="icon icon-file"></i>&nbsp;&nbsp;  Templates', array('class' => 'btn btn-success'));?>
        <?php echo anchor('schedule','<i class="icon icon-arrow-left"></i>&nbsp;&nbsp;  Back', array('class' => 'btn btn-default'));?>
      </div>
    </div>
  </form>

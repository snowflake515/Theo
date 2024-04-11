<?php
$id_patient = $this->EncounterHistoryModel->get_by_id($id_encounter)->row();
$id_patient = ($id_patient && $id_patient->Patient_ID) ? $id_patient->Patient_ID : 0;
$dt_patien = $this->PatientProfileModel->get_by_id($id_patient)->row();
if ($dt_patien):
  ?>
  <div class="page-header">
    <h1>
      Template For "<?php echo $dt_patien->LastName . ', ' . $dt_patien->FirstName ?>"
    </h1>
  </div>

  <div class="widget-box">
    <div class="widget-header">
      <h4>Template &raquo; <?php echo $dt->TML1_Description ?> &raquo; Level 2 </h4>
    </div>
    <div class="widget-body">
      <form class="form-horizontal" role="form" method="post" action="<?php echo site_url('template/tml3') ?>" accept-charset="UTF-8">

        <!-- Default panel contents -->

        <div class="panel-body">
          <?php
          echo form_hidden('id_encounter', $id_encounter);
          echo form_hidden('id_tml1', $dt->TML1_ID);

          foreach ($template2 as $val) {
            echo '<div class="checkbox">
     ' . form_checkbox('template[]', $val->TML2_ID, $val->PreSelected, 'class="checkBox"    data-label="' . $val->TML2_Description . '"') .
            '</div> ';
          }
          ?>
        </div>
        <div class="panel-footer">
          <div >
            <button type="button" onclick="window.history.go(-1)" class="btn btn-default"><i class="icon icon-arrow-left"></i>&nbsp;&nbsp; Back</button>
            <button type="submit" class="btn btn-primary">Next &nbsp;&nbsp; <i class="icon icon-arrow-right"></i></button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <?php
else:
  echo '<div class="page-header">
    <h1>
      Error : No Patient!
    </h1>
  </div>';
endif;
?>
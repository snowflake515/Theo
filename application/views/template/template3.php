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
      <h4>Template &raquo; <?php echo $dt->TML1_Description ?> &raquo; Level 3 </h4>
    </div>
    <div class="widget-body clearfix">

        <input type="hidden" value="<?php echo $id_encounter ?>" id="id_encounter">
        <input type="hidden" value="<?php echo $tml1 ?>" id="tml_1">

        <!-- Default panel contents -->

        <?php
        $con = '(Hidden = 0 OR Hidden IS NULL)';
        foreach ($post as $value) {
          $tml_2 = $this->Tml2Model->get_by_id($value)->row();
          echo '<h4 class="bold_label">' . $tml_2->TML2_Description . '<h4>';
          $tml_3 = $this->Tml3Model->get_by_field('TML2_ID', $value, $con)->result();
          $prep_ins = FALSE;
          $chek_tml2 = $this->TabletInputModel->get_by_field('TML2_ID', $value, 'Encounter_ID = ' . $id_encounter);
          if ($chek_tml2->num_rows() == 0) {
            $prep_ins = TRUE;
          }
          foreach ($tml_3 as $item) {


            if ($item->PreSelected == 1) {
              $dt_insert = array(
                  'Encounter_Id' => $id_encounter,
                  'TML3_Id' => $item->TML3_ID,
              );

              $cek_etl3 = $this->ETL3Model->get_by_field('Encounter_ID', $id_encounter, 'TML3_Id = ' . $item->TML3_ID);
              if ($cek_etl3->num_rows() == 0) {
                $this->ETL3Model->insert($dt_insert);
              }

              $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_ID', $id_encounter, 'TML3_Id = ' . $item->TML3_ID);
              if ($cek_tml3_input->num_rows() == 0) {
                $this->ETL3InputModel->insert($dt_insert);
              }

              $chek_tml3 = $this->TabletInputModel->get_by_field('TML3_ID', $item->TML3_ID, 'Encounter_ID = ' . $id_encounter);
              if ($prep_ins && $chek_tml3->num_rows() == 0) {
                $dt_inset = array(
                    'Encounter_ID' => $id_encounter,
                    'TML3_ID' => $item->TML3_ID,
                    'TML1_ID' => $tml1,
                    'TML2_ID' => $value,
                    'TML3_Value' => NULL
                );
                $chek_tml2 = $this->TabletInputModel->insert($dt_inset);
              }
            }


            $data_check = $this->TabletInputModel->get_data($item->TML3_ID, $id_encounter)->row();
            $check_data = ($data_check && $data_check->Status !== 'X') ? TRUE : FALSE;

            if (strtolower(trim($item->TML3_Description)) == "[input]") {
              echo '<div class="checkbox">
                    <label>'
                    . form_checkbox(array('id' => "check" . $item->TML3_ID,
                    'name' => 'template3[]',
                    'onchange' => "save_template3(this)",
                    'data-tml2' => $value),
                    $item->TML3_ID,
                    $check_data,
                    'class="checkBox" data-label="' . $item->TML3_Description . '" ' )
                    . form_input(
                    array('maxlength' => 100,
                    'class' => 'form-control input_check',
                    'data-target' => "#check" . $item->TML3_ID,
                    'name' => 'TML3_Value',
                    'data-tmp' => ($data_check) ? $data_check->TML3_Value : NULL,
                    'id' => $item->TML3_ID,
                    'value' => ($data_check) ? $data_check->TML3_Value : NULL)) . '</label>
                    </div> ';
            } else {
              echo '<div class="checkbox">
                    <label>' . form_checkbox(array('id' => "check" . $item->TML3_ID, 'name' => 'template3[]', 'onchange' => "save_template3(this)", 'data-tml2' => $value), $item->TML3_ID, $check_data, 'class = "checkBox"    data-label="' . $item->TML3_Description . '"') . ' ' . '' . '</label>
                    </div>  ';
            }
          }
          echo '<hr/>';
        }
        ?>

        <div class="panel-footer">
          <div class="">
            <button type="button" onclick="window.history.go(-1)" class="btn btn-default"><i class="icon icon-arrow-left"></i>&nbsp;&nbsp; Back</button>
            <a href="<?php echo site_url('clinical_trigger/encounter/' .$id_encounter)  ?>" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;&nbsp; Save</a>
          </div>
        </div>


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

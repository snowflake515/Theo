<?php

$encounter_ID = (int) $this->input->post('Encounter_ID');
$tml1_id = (int) $this->input->post('tml1_ID');
if ($encounter_ID && $tml1_id) {

  //$con = '(Hidden = 0 OR Hidden IS NULL) AND PreSelected = 1';
  $con = '(Hidden = 0 OR Hidden IS NULL)';
  if (isset($tml2_arr)) {
    $ids = (!empty($tml2_arr)) ? $tml2_arr : 0;
    // $con = "(Hidden = 0 OR Hidden IS NULL) AND TML2_ID IN ($ids)";
  }
  if ($tml1) {
    $template2 = $this->Tml2Model->get_by_field('TML1_ID', $tml1, $con)->result();
    $con = '(Hidden = 0 OR Hidden IS NULL)';
    $options = array(
        "realnumber" => "* Real Number eg:99.99",
        "integer" => "* Integer, max length 3, eg:123",
        "letters_only" => "* Letters Only",
        "alphanumeric" => "* AlphaNumeric",
    );

    foreach ($template2 as $val) {
      $tml2_show = ($val->PreSelected == 1) ? '' : 'hide';
      echo '<div id="tml2-wrap-'.$val->TML2_ID.'" class="'.$tml2_show.'">' ;
      echo '<h4>' . $val->TML2_Description . '</h4>';

      $tml_3 = $this->Tml3Model->get_by_field('TML2_ID', $val->TML2_ID, $con)->result();
      echo '<ul class="list-unstyled">';  

      

      foreach ($tml_3 as $tml3) {

        $des = $tml3->TML3_Description;
        $TML3_ValueInput = NULL;
        $help_box = NULL;
        $enable_ins = "";
        $tm3_InstructionLabel = "";

        if ($tml3->PreSelected == 1 && $tml3->TypeInput != 'radio_btn') {

          $dt_insert = array(
              'Encounter_Id' => $encounter_ID,
              'TML3_Id' => $tml3->TML3_ID,
          );

          $cek_etl3 = $this->ETL3Model->get_by_field('Encounter_ID', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
          if ($cek_etl3->num_rows() == 0) {
            $this->ETL3Model->insert($dt_insert);
          }

          $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_ID', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
          if ($cek_tml3_input->num_rows() == 0) {
            $this->ETL3InputModel->insert($dt_insert);
          }


          $check_tabletinput = $this->TabletInputModel->get_by_field('TML3_ID', $tml3->TML3_ID, 'Encounter_ID = ' . $encounter_ID);
          if ($check_tabletinput->num_rows() == 0) {
            $dt_inset = array(
                'Encounter_ID' => $encounter_ID,
                'TML1_ID' => $tml1_id,
                'TML2_ID' => $tml3->TML2_ID,
                'TML3_ID' => $tml3->TML3_ID,
                'TML3_Value' => NULL
            );
            $this->TabletInputModel->insert($dt_inset);
          }
        }

        $data_check = $this->TabletInputModel->get_data($tml3->TML3_ID, $encounter_ID)->row();
        $check_cb = ($data_check && $data_check->Status !== 'X') ? TRUE : FALSE;
        $checked = ($check_cb) ? 'checked' : '';
        $TML3_ValueInput = ($check_cb) ? $data_check->TML3_Value : NULL;


        echo '<li id="li_tml3_' . $tml3->TML3_ID . '">';
        if($tml3->SubTitle == 1){
          echo '<div class="sub-title"> '.$des.' </div>';
        }elseif ($tml3->TypeInput == 'radio_btn' && !empty($tml3->RadioName)) {
          echo '<div class="radio cstm-cb "><label><input id="cbx_' . $tml3->TML3_ID . '"
          name="' . $tml3->RadioName . '"
          type="radio" data-tml2id="' . $tml3->TML2_ID . '"
          data-forceselect="' . $tml3->ForceSelect_ID . '"
          data-tml3id="' . $tml3->TML3_ID . '"
          data-tml3_tbotmaster_id="' . $tml3->TML3_TBotMaster_ID . '"
          data-theoquestion_id="' . $tml3->TheoQuestion_ID . '"
          data-theoanswer_id="' . $tml3->TheoAnswer_ID . '"
          data-theovideoplay_id="' . $VideoPlay_ID . '"
          data-theosession_id="' . $Session_ID . '"
          data-theoaccount_id="' . $Account_ID . '"
          onclick="change_new_tml3(this)" value="' . $tml3->TML3_ID . '"  ' . $checked . ' > <span><i class="icon icon-circle-blank"></i><i class="icon icon-circle"></i></span> ' . $des . ' </label></div>';
        } else {
          //431 is total phq9;
          //1147 is total phq2;
          $disable_click = ($tml3->TML3_TBotMaster_ID == 431 || $tml3->TML3_TBotMaster_ID == 1147) ?  'disabled' : NULL;
          if (strtolower(trim($tml3->TML3_Description)) == '[input]') {
            $data_tbot = 'data-tbotmaster="'.$tml3->TML3_TBotMaster_ID.'" data-tbotdata="'.$tml3->TML3_TBotData.'"';
            $des = ' <input
                        maxlength="500" '.$disable_click.'
                        type="text"
                        data-forceselect="' . $tml3->ForceSelect_ID . '" '.$data_tbot.'
                        id="input_' . $tml3->TML3_ID . '"
                        onfocus="check_old_val(this)"
                        data-mask="' . $tml3->MaskInput . '"
                        onblur="change_new_tml3_input(this)"
                        data-tml3id="' . $tml3->TML3_ID . '"
                        class=" form-control f-cstm-input"
                        value="' . $TML3_ValueInput . '" />';
            if (isset($options[$tml3->MaskInput])) {
              $help_box = '<p class="help-block">' . $options[$tml3->MaskInput] . '</p>';
            }
          }
          if ($tml3->TypeInput == 'checkbox' && $tml3->EnableInstruction == 1 && !empty($tml3->InstructionLabel)) {
            $enable_ins = 'data-enableins="1"';

            $tm3_InstructionLabel = '<input type="hidden" id="tm3_InstructionLabel_' . $tml3->TML3_ID . '" value="' . $tml3->InstructionLabel . '">';
          }
          $cls = ($help_box) ? 'no-margin' : NULL;
          echo '<div class="checkbox cstm-cb ' . $cls . '">
                  <label>
                    <input ' . $enable_ins . ' '.$disable_click.'
                      data-forceselect="' . $tml3->ForceSelect_ID . '"
                      data-pforceselect="' . $tml3->PForceSelect_ID . '"
                      id="cbx_' . $tml3->TML3_ID . '"
                      type="checkbox"
                      data-tml2id="' . $tml3->TML2_ID . '"
                      data-tml3id="' . $tml3->TML3_ID . '"
                      data-tml3_tbotmaster_id="' . $tml3->TML3_TBotMaster_ID . '"
                      data-theoquestion_id="' . $tml3->TheoQuestion_ID . '"
                      data-theoanswer_id="' . $tml3->TheoAnswer_ID . '"
                      data-theovideoplay_id="' . $VideoPlay_ID . '"
                      data-theosession_id="' . $Session_ID . '"
                      data-theoaccount_id="' . $Account_ID . '"
                      onclick="change_new_tml3(this)"
                      value="' . $tml3->TML3_ID . '"
                      ' . $checked . '
                      onclick="change_new_tml3(this)">
                    <span>
                    <i class="icon icon-check"></i>
                    <i class="icon icon-unchecked"></i>
                    </span> ' . $des . '
                    </label></div>';
          echo $help_box;
          echo $tm3_InstructionLabel;
        }
        echo '</li>';
      }
      echo '</ul>';
      echo "</div>";
    }
 
  }
}

<?php

  $sql = "Select Distinct T1.TML1_ID, T1.Sequence, T1.TML1_Description, T1.Hidden 
    From $data_db.dbo.TabletInput TI 
    JOIN $template_db.dbo.TML1 T1 ON TI.TML1_ID=T1.TML1_ID
    JOIN $template_db.dbo.TML3 T3 ON T3.TML3_ID =TI.TML3_ID 
    JOIN $template_db.dbo.TML2 T2 ON T2.TML2_ID = TI.TML2_ID  
    Where T2.TML2_HeaderMaster_ID=$HeaderMasterKey
      AND (T1.Hidden<>1 OR T1.Hidden IS NULL)
      AND (T2.Hidden<>1 OR T2.Hidden IS NULL) And (T3.Hidden<>1 OR T3.Hidden IS NULL) 
      AND Encounter_ID = $PrimaryKey";

    $CountTML1IDs = $this->ReportModel->data_db->query($sql);
    $CountTML1IDs_num = $CountTML1IDs->num_rows();
    $CountTML1IDs_row = $CountTML1IDs->row();
    $CountTML1IDs_result = $CountTML1IDs->result();
    
    $extractResult = '';

    foreach ($CountTML1IDs_result as $CountTML1IDs_dt) {
      $abnormal = (isset($summary_report) ? 'And T3.Abnormal = 1  ' : "");

      $sql = "Select (SELECT  CONVERT(varchar(10), TML2_ID)+ ','  
        From ETL2
        Where Encounter_ID=$PrimaryKey  FOR XML PATH('') ) as ids";

      $ETL2IDS = $this->ReportModel->data_db->query($sql);
      $ETL2IDS_row = $ETL2IDS->row();


      $sql = "Select (SELECT CONVERT(varchar(10), TML3_ID)+ ','  
        From ETL3
        Where Encounter_ID=$PrimaryKey  FOR XML PATH('') ) as ids";

      $ETL3IDS = $this->ReportModel->data_db->query($sql);
      $ETL3IDS_row = $ETL3IDS->row();

      $ETL2IDS_arr = !empty($ETL2IDS_row->ids) ? $ETL2IDS_row->ids.'0' : 0;
      $ETL3IDS_arr = !empty($ETL3IDS_row->ids) ? $ETL3IDS_row->ids.'0' : 0;

      $sql = "Select T2.TML2_ID,
          T2.TML2_Sentence,
          T3.TML3_TextToType,
          T3.TML3_ID,
          T3.TML3_TBotMaster_ID,
          T3.TheoQuestion_ID,
          T3.TheoAnswer_ID
          From $template_db.dbo.TML2 T2
          JOIN $template_db.dbo.TML3 T3 ON T2.TML2_ID=T3.TML2_ID
          JOIN $template_db.dbo.TML1 T1 ON T2.TML1_ID=T1.TML1_ID
          Where T2.TML2_HeaderMaster_ID=$HeaderMasterKey
          AND T1.TML1_ID = " . $CountTML1IDs_dt->TML1_ID . "
          And T2.TML2_ID IN ($ETL2IDS_arr)
          And T3.TML3_ID IN ($ETL3IDS_arr)
          And (T2.Hidden<>1 OR T2.Hidden IS NULL)
          And (T3.Hidden<>1 OR T3.Hidden IS NULL) 
          $abnormal
          Order By T1.Sequence, T2.Sequence, T3.Sequence";

      $TML = $this->ReportModel->data_db->query($sql);
      $TML_num = $TML->num_rows();
      $TML_row = $TML->row();
      $TML_result = $TML->result();

      if ($TML_num != 0) {
        if ($OutPutMasterKey != $HeaderMasterKey) {
          if ($NeedTemplateHeader == TRUE) {
            $sql = "	Select TOP 1
                      H.Header_ID,
                      H.HeaderMaster_ID,
                      H.HeaderText,
                    H.HeaderStyle,
                    H.HeaderSize,
                    H.HeaderColor,
                    F.FontName
                  From EncounterHeaders H
                  Join Fonts F
                    On H.Font_ID=F.Font_ID
                Where H.HeaderMaster_ID=$HeaderMasterKey
                  And H.Provider_ID=$ProviderKey
                      And H.EncounterDescription_ID=$EncounterDescriptionKey
                  And (H.Hidden<>1 OR H.Hidden IS NULL)";
            $HeaderSettingsTemplate = $this->ReportModel->data_db->query($sql);
            $HeaderSettingsTemplate_num = $HeaderSettingsTemplate->num_rows();
            $HeaderSettingsTemplate_row = $HeaderSettingsTemplate->row();
            $HeaderSettingsTemplate_result = $HeaderSettingsTemplate->result();

            if ($HeaderSettingsTemplate_num != 0) {

              $OutputMasterKey = $HeaderSettingsTemplate_row->HeaderMaster_ID;
              $FontColor = $HeaderSettingsTemplate_row->HeaderColor;
              $FontSize = $HeaderSettingsTemplate_row->HeaderSize;
              $FontFace = $HeaderSettingsTemplate_row->FontName;

              if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'B') !== false) {
                $FontWeight = "font-weight: bold;";
              } else {
                $FontWeight = "";
              }

              if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'I') !== false) {
                $FontStyle = "font-style: italic;";
              } else {
                $FontStyle = "";
              }

              if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'U') !== false) {
                $FontDecoration = "text-decoration: underline;";
              } else {
                $FontDecoration = "";
              }

              $des = "";
              if ($HeaderSettingsTemplate_row->HeaderText != "") {
                if ($CountTML1IDs_num > 1) {
                  $des = ':<span > ' . $CountTML1IDs_dt->TML1_Description . ' </span><br/>';
                }
              }
              echo '<span style="color:#' . trim($FontColor) . '; font-size:' . trim($FontSize) . 'px;  font-family:' . trim($FontFace) . '; ' . trim($FontWeight) . '  ' . trim($FontStyle) . ' ' . trim($FontDecoration) . '">
                    ' . trim($HeaderSettingsTemplate_row->HeaderText) . '
                    ' . $des . '
                  </span>';

              $extractResult = $extractResult . "Here is " . trim($HeaderSettingsTemplate_row->HeaderText) . ". ";
            }
          }
        }
      }

      if ($TML_num != 0) {

        $data['data_db'] = $data_db;
        $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
        $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";

        $CurrentSentence = "";
        $s_tml2 = array();
        $s_theo = '';
        $i = 0;
        $join_text_tml3 = array();
        $key_order = array();
        foreach ($TML_result as $TML_dt) {
          $i++;
          if (trim($TML_dt->TML2_Sentence) != "" && trim($TML_dt->TML3_TextToType) != "") {

            $CurrentSentence = trim(strip_tags($TML_dt->TML2_Sentence, '<b><br><font>')) . ': ';

            if (trim(strtolower($TML_dt->TML3_TextToType)) != "[input]") {

              $join_text_tml3[] = trim(strip_tags($TML_dt->TML3_TextToType, '<b><br><font>'));
            } else {

            $data_check = $this->TabletInputModel->get_data($TML_dt->TML3_ID, $PrimaryKey)->row();
            $check_cb = ($data_check && $data_check->Status !== 'X') ? TRUE : FALSE;
            if($check_cb){
              if($TML_dt->TML3_TBotMaster_ID == 325){
                $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_ID', $PrimaryKey, 'TML3_Id = ' . $TML_dt->TML3_ID)->row();
                if(!empty($cek_tml3_input->ETL3Input)){
                  $join_text_tml3[] = '__[input]' . trim( $cek_tml3_input->ETL3Input ); 
                }
              }else{
                $join_text_tml3[] = '__[input]' . trim( $data_check->TML3_Value ); 
              }    
            } 
            }
          }
        }

        $CurrentSentence =    $CurrentSentence . ' ' . implode(', ', $join_text_tml3);
      
      $CurrentSentence = mb_convert_encoding($CurrentSentence, "HTML-ENTITIES", "UTF-8");
      
        $search  = array("..", ".:", ":,", "-,", "., ",  ", __[input]", "__[input]", "&nbsp;,");
        $replace = array(".", ":", ":", "", "", "", "", "",);
      
      foreach ($search as $k => $v) {
      if (strpos($CurrentSentence, $search[$k]) === FALSE)  
        unset($search[$k], $replace[$k]);
      }                                          
      if ($search) $CurrentSentence = str_replace($search, $replace, $CurrentSentence);
      $extractResult = $extractResult . strip_tags($CurrentSentence) . ". ";
        echo '<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
              <tr>
                <td width="6"></td>
                <td align="left" style="width: 7.0in; ' . $DefaultStyle . ' valign="top">
                  ' . $CurrentSentence . '
                </td>
              </tr>
            </table>
            <input value="%%%###'. $extractResult .'%%%###" style="display: none;"></input>
            ';
      }
  }
// log_message('error', $extractResult);
  setcookie("inputResult", $extractResult);
?>
<?php
$abnormal = (isset($summary_report) ? 'And T3.Abnormal = 1  ' : "");
$sql =  "Select  
T1.Sequence t1s , T2.Sequence  t2s ,  T3.Sequence t3s,
T2.TML2_ID,
        T2.TML2_Sentence,
        T3.TML3_TextToType, T3.TML3_ID, 
        T3.TheoQuestion_ID, 
        T3.TheoAnswer_ID,
        TI.TabletInput_ID,
        TI.TML1_ID,
        TI.TML3_Value 
        From $data_db.dbo.TabletInput TI
        JOIN $template_db.dbo.TML3 T3 ON T3.TML3_ID =TI.TML3_ID 
        JOIN $template_db.dbo.TML2 T2 ON T2.TML2_ID = TI.TML2_ID 
        JOIN $template_db.dbo.TML1 T1 ON T1.TML1_ID=TI.TML1_ID 
        Where T2.TML2_HeaderMaster_ID=$HeaderMasterKey 
        AND TI.Encounter_ID = $PrimaryKey
        AND (TI.Status <> 'X')
        $abnormal
        AND (T2.Hidden<>1 OR T2.Hidden IS NULL) And (T3.Hidden<>1 OR T3.Hidden IS NULL) 
        Order By T1.Sequence, T2.Sequence, T3.Sequence";

$TML = $this->ReportModel->data_db->query($sql);
$TML_num = $TML->num_rows();
$TML_row = $TML->row();
$TML_result = $TML->result();

$TML2Struct = array();
foreach ($TML_result as $TML_dt) {
  $TML2Struct["$TML_dt->TML2_Sentence"] = $TML_dt->TML3_ID;
}

if ($TML_num != 0) {
  if ($OutPutMasterKey != $HeaderMasterKey) {
    if ($NeedTemplateHeader == TRUE) {
      $sql = "Select TOP 1
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
          $des = '<br/>';
        }
        echo '<span style="color:#' . trim($FontColor) . '; font-size:' . trim($FontSize) . 'px;  font-family:' . trim($FontFace) . '; ' . trim($FontWeight) . '  ' . trim($FontStyle) . ' ' . trim($FontDecoration) . '">
                 ' . trim($HeaderSettingsTemplate_row->HeaderText) . '
                 ' . $des . '
              </span>';
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
      $CurrentSentence = trim($TML_dt->TML2_Sentence) . ' ';
      $key_order_id = $TML_dt->t1s . '_' . $TML_dt->t2s . '_' . $TML_dt->t3s;
      if (trim(strtolower($TML_dt->TML3_TextToType)) != "[input]") {
        $t_v = trim(strip_tags($TML_dt->TML3_TextToType));
        $key_s = array_search($t_v, array_column($key_order, 'value'));
        if (!array_key_exists($key_order_id, $key_order) && $key_s === false) {
          $key_order[$key_order_id] = array(
            'TabletInput_ID' => $TML_dt->TabletInput_ID,
            'value' =>  $t_v
          );
        }
      } else {
        if (!array_key_exists($key_order_id, $key_order) || $TML_dt->TabletInput_ID > $key_order[$key_order_id]['TabletInput_ID']) {
          $t_v = trim(strip_tags($TML_dt->TML3_Value));
          $key_order[$key_order_id] =  array(
            'TabletInput_ID' => $TML_dt->TabletInput_ID,
            'value' => '__[input]' . $t_v
          );
        }
      }
    }
  }


  foreach ($key_order as $key => $t) {
    $join_text_tml3[] = $t['value'];
  }

  $count_join = count($join_text_tml3);
  if ($count_join >= 2) {
    $join_text_tml3[$count_join - 1] = 'and ' . $join_text_tml3[$count_join - 1];
  }

  $CurrentSentence =    $CurrentSentence . ' ' . implode(', ', $join_text_tml3);
  if ($count_join) {
    $CurrentSentence =    $CurrentSentence . '.';
  }

  $search  = array("-,", ".,", "..", ", __[input]", "__[input]");
  $replace = array("", "", ".", "", "");

  $CurrentSentence = str_replace($search, $replace, $CurrentSentence);

  echo '<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
          <tr>
            <td width="6"></td>
            <td align="left" style="width: 7.0in; ' . $DefaultStyle . ' valign="top"> 
              ' . $CurrentSentence . ' 
            </td>
          </tr>
        </table>';
}

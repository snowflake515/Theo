<?php
 
$ProviderKey = (int) $dt_encounter->Provider_ID;
$EncounterDescriptionKey = (int) $dt_encounter->EncounterDescription_ID;

 $sql = "							Select TOP 1
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
							 Where H.HeaderMaster_ID=139
							   And H.Provider_ID=$ProviderKey
	   						   And H.EncounterDescription_ID=$EncounterDescriptionKey
							   And (H.Hidden<>1 OR H.Hidden IS NULL)";
               

$Records = $this->ReportModel->data_db->query($sql);
$Records_num = $Records->num_rows();
$Records_row = $Records->row();

if ($Records_num != 0) {
  $FontColor = "color : #$Records_row->HeaderColor;";
  $FontSize = "font-size: $Records_row->HeaderSize" . "px;";
  $FontFace = "font-family: $Records_row->FontName;";

  $FontWeight = "";
  if ($Records_row->HeaderStyle == "B") {
    $FontWeight = "font-weight: bold;";
  } else if ($Records_row->HeaderStyle == "I") {
    $FontWeight = "font-style: italic;";
  } else if ($Records_row->HeaderStyle == "U") {
    $FontWeight = "text-decoration: underline;";
  }

  if ($Records_row->HeaderMaster_ID == 1) {
    echo "<br>";
  }
  echo "<span style = '" . trim($FontColor) . " " . trim($FontSize) . " " . trim($FontFace) . " " . trim($FontWeight) . " '>";
  echo trim($Records_row->HeaderText);
  if ($Records_row->HeaderText != "") {
    echo "<br>";
  }
  echo "</span>";

//
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $Records_row->Header_ID);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
   
  ?>


  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td style="<?php echo $DefaultStyle; ?>">
        <?php
        echo $dt_encounter->ChiefComplaint;
        ?>
      </td>
    </tr>
  </table>
  <?php
}
?>
<?php

$sql = "	Select
		asm.Description,
		asm.SeverityDescription,
		asm.Treatment,
		asm.AssociatedRisk
	From AWACSResults as ar
	Join eCastMaster.dbo.AWACSSeverityMaster as asm
		on asm.AWACSSeverityMaster_ID = ar.AWACSSeverityMaster_ID
	Join PatientProfile as pp
		on ar.patient_id = pp.patient_id
	Where (ar.Encounter_Id = $ComponentKey)
		And (ar.Hidden = 0)
		And ((pp.sex <> 'F') or ((pp.sex = 'F') and (asm.AWACSDiseaseMaster_Id <> 16))) ";

$getAWACSResults = $this->ReportModel->data_db->query($sql);
$getAWACSResults_num = $getAWACSResults->num_rows();
$getAWACSResults_result = $getAWACSResults->result();

if ($getAWACSResults_num != 0) {

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
//  }

  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: ". $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] .  "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
       The patient's relative risk factors are shown in the following table.<br>
      </td>
    </tr>
    <tr>
      <td width="7">&nbsp;</td>
      <td>
        <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
          <tr>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top" nowrap>
              CHRONIC CONDITION
            </td>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?> min-width: 70px; text-align: center; border-style:solid; border-width:3px;  padding:2px;" valign="top" nowrap>
              LEVEL
            </td>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
              TREATMENT OPTIONS
            </td>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top" nowrap>
              ASSOCIATED RISKS
            </td>
          </tr>

          <?php foreach ($getAWACSResults_result as $val) { ?>
            <tr>
              <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                <?php echo $val->Description; ?>&nbsp;
              </td>
              <td align="center" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                <?php echo $val->SeverityDescription; ?>&nbsp;
              </td>
              <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                <?php echo $val->Treatment; ?>&nbsp;
              </td>
              <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                <?php echo $val->AssociatedRisk; ?>&nbsp;
              </td>
            </tr>
          <?php } ?>

        </table>
      </td>
    </tr>
  </table>

  <?php
}


?>

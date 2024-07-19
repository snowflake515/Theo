<?php

$sql = "	Select
		Category,
		Year1,
		Year2,
		Year3,
		Year4,
		Year5
	From ecastmaster.dbo.AWACSScreeningMaster
	Where (Severity = 0)
		And (Hidden = 0)
	Order by SortOrder";

$getAWACSScreening = $this->ReportModel->data_db->query($sql);
$getAWACSScreening_num = $getAWACSScreening->num_rows();
$getAWACSScreening_result = $getAWACSScreening->result();

if ($getAWACSScreening_num != 0) {

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);

  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
    <cfoutput>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
        The patient's written screening schedule and 5-year plan is as follows.
      </td>
      </tr>
      <tr>
        <td width="7">&nbsp;</td>
        <td>
          <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
            <tr>
              <td nowrap align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                CATEGORY
              </td>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                GOALS
              </td>
            </tr>

            <?php foreach ($getAWACSScreening_result as $val) { ?>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                  <?php echo $val->Category; ?>&nbsp;
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year1; ?>&nbsp;
                </td>
              </tr>
            <?php } ?>

          </table>
          <?php
        }
        ?>

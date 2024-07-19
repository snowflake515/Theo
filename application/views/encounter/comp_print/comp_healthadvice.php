<?php

$PrintPHAOnly = 0; 
$PatientLang = 1;

if ($PrintPHAOnly) {

  $sqlPatientLang = "SELECT 	Top 1
				ISNULL(LanguageMaster_ID,1) AS PatientLang
		FROM	" . $data_db . ".dbo.PatientProfile
		WHERE	Patient_id = $PatientKey";

  $PatientLang = $this->ReportModel->data_db->query($sqlPatientLang);
  $PatientLang_row = $PatientLang->row();

  $sqlVerifyPatientLang = "		SELECT	Top 1
				LanguageMaster_ID
		FROM	" . $data_db . ".dbo.LanguageMaster
		WHERE	LanguageMaster_ID = $PatientLang_row->PatientLang
				AND (hidden = 0 OR hidden is null)";


  $VerifyPatientLang = $this->ReportModel->data_db->query($sqlVerifyPatientLang);
  $VerifyPatientLang_num = $VerifyPatientLang->num_rows();
  $VerifyPatientLang_row = $VerifyPatientLang->row();

  if ($VerifyPatientLang_num > 1) {
    $PatientLang = 1;
  } else {
    if ($PatientLang_row->PatientLang == 4) {
      $PatientLang = 1;
    } else if ($PatientLang_row->PatientLang == 3) {
      $PatientLang = 1;
    } else {
      $PatientLang = $PatientLang_row->PatientLang;
    }
  }
}

$ComponenKeyVar = $ComponentKey;

$sql_check_org = "select top 1 * from ecastmaster.dbo.awacsadvicemaster where Org_ID = $Encounter_dt->Org_ID ";
$ch = $this->ReportModel->data_db->query($sql_check_org);
$isset_org_id = ($ch->num_rows() > 0 ) ? "AND (am.Org_ID = $Encounter_dt->Org_ID OR am.Org_ID is NULL)" : "AND am.Org_ID is NULL";

$sqlgetAWACSAdvice = "	select
		al_L.DisplayName as category,
		am_L.recommendation,
		am_L.accomplishby,
		al_L.sortorder as CategorySortOrder,
		am_L.sortorder as AdviceSortOrder,
		am_L.LanguageMaster_ID
	From ecastmaster.dbo.awacsadvicemaster As am
		Join ecastmaster.dbo.L_awacsadvicemaster As am_L
			On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
		Inner Join ecastmaster.dbo.awacsadvicelist As al
			On am.awacsadvicelist_id = al.awacsadvicelist_id
		Join ecastmaster.dbo.L_awacsadvicelist As al_L
			On al.awacsadvicelist_id = al_L.awacsadvicelist_id
		Left join ecastmaster.dbo.awacsadvicemap As map
			On am.awacsadvicemaster_ID = map.awacsadvicemaster_ID
		Left join awacsinput As ai
			on (ai.awacsriskmaster_ID = map.awacsriskmaster_ID and ai.awacsriskmaster_ID is not null)
				or (ai.tbotmaster_ID = map.tbotmaster_ID and ai.tbotmaster_ID is not null)
	Where
    ai.encounter_ID = $ComponenKeyVar AND
    ai.datavalue = am.severity AND
    ai.hidden = 0
		AND am_L.LanguageMaster_ID = $PatientLang
		AND al_L.LanguageMaster_ID = $PatientLang
    $isset_org_id
	Union
	Select
		al_L.DisplayName as category,
		am_L.recommendation,
		am_L.accomplishby,
		al_L.sortorder as CategorySortOrder,
		am_L.sortorder as AdviceSortOrder,
		am_L.LanguageMaster_ID
	From awacsresults As ar
	Join ecastmaster.dbo.awacsadvicemap As map
		On map.awacsseveritymaster_ID = ar.awacsseveritymaster_ID
	join ecastmaster.dbo.awacsadvicemaster As am
		On map.awacsadvicemaster_ID = am.awacsadvicemaster_ID
	Join ecastmaster.dbo.L_awacsadvicemaster As am_L
		On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
	Inner Join ecastmaster.dbo.awacsadvicelist As al
		On am.awacsadvicelist_id = al.awacsadvicelist_id
	Join ecastmaster.dbo.L_awacsadvicelist As al_L
		On al.awacsadvicelist_id = al_L.awacsadvicelist_id
	Where (ar.encounter_ID = $ComponenKeyVar)
		And ar.hidden = 0
		And am.hidden = 0
		AND am_L.LanguageMaster_ID = $PatientLang
		AND al_L.LanguageMaster_ID = $PatientLang
    $isset_org_id
	Order by CategorySortOrder, AdviceSortOrder	";

$AWACSAdvice = $this->ReportModel->data_db->query($sqlgetAWACSAdvice);
$AWACSAdvice_num = $AWACSAdvice->num_rows();
$AWACSAdvice_result = $AWACSAdvice->result();

if ($AWACSAdvice_num != 0) {

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

  $sql = "SELECT	AWACSAdviceHeader_ID,
  				SortOrder,
  				HeaderText
  		FROM	ecastmaster.dbo.L_AWACSAdviceHeader
  		WHERE	LanguageMaster_ID = $PatientLang";

  $getAWACSAdviceHeaders = $this->ReportModel->data_db->query($sql);
  $getAWACSAdviceHeaders_num = $getAWACSAdviceHeaders->num_rows();
  $getAWACSAdviceHeaders_result = $getAWACSAdviceHeaders->result();

  $ColHead1 = "";
  $ColHead2 = "";
  $ColHead3 = "";

  $n = 1;
  foreach ($getAWACSAdviceHeaders_result as $v) {
    if ($n == 1) {
      $ColHead1 = $v->HeaderText;
    } else if ($n == 2) {
      $ColHead2 = $v->HeaderText;
    } else {
      $ColHead3 = $v->HeaderText;
    }
    $n++;
  }
  ?>
  <table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php
        if ($PatientLang == 1) {
          echo "The patient's personalized health advice is as follows.";
        } else {
          echo "&nbsp;";
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>

        <?php
        $RowBorderBegin = "  border:solid 3px; border-left:solid 3px; border-right:solid 3px; border-bottom:solid 3px; padding:2px;";
        $RowBorder = " ; border-top:solid 3px; border-bottom:solid 3px; border-right:solid 3px; padding:2px;";
        $RowBorderEnd = "  border-right:solid 3px;   border-bottom:solid 3px; padding:2px;";
        $RowBorderBull = "border-bottom:solid 3px;";
        ?>
        <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
          <tr>
            <td nowrap align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <?php
              echo strtoupper($ColHead1);
              ?>
            </td>
            <td  colspan="2" align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <?php
              echo strtoupper($ColHead2);
              ?>
            </td>
            <td nowrap align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <?php
              echo strtoupper($ColHead3);
              ?>
            </td>
          </tr>

          <?php
          $tmp = "";
          $tmp_arr = array();
          $n = 0;
          foreach ($AWACSAdvice_result as $ar) {
            $tmp_arr[$ar->category][] = $ar->recommendation;
          }


          foreach ($tmp_arr as $key => $v) {

            if ($tmp != $key) {
              ?>
              <tr>
                <td rowspan="<?php echo sizeof($v); ?>"  nowrap align="left"  valign="center" style="<?php echo $RowBorderBegin .$DefaultStyle; ?>">
                  <?php echo $key . "&nbsp"; ?>
                </td>
                <?php
              }
              $p = 0;
              $border = "font-size:14px; padding:3px;";

              foreach ($v as $k) {
                if ($p > 0) {
                  echo '<tr>';
                }
                if (sizeof($v) == ($p + 1)) {
                  $border .= "border-bottom:solid 3px;";
                }
                ?>
                <td align="left"  valign="top" style="<?php echo $border. ' ' .$DefaultStyle; ?>">
                  &bull;
                </td>
                <td align="left"  valign="top" style="<?php echo $border. ' ' .$DefaultStyle; ?>">
                  <?php echo "$k"; ?>
                </td>
                <?php
                if ($p > 0) {
                  echo '</tr>';
                } else {
                  ?>
                  <td rowspan="<?php echo sizeof($v); ?>" align="left"  valign="center" style="<?php echo $RowBorderBegin. ' ' .$DefaultStyle; ?>;">
                    <?php
                    if ($PatientLang == 2) {
                      echo '&nbsp;Como le indique<br>&nbsp;el proveedor de:<br><br>&nbsp;______________	';
                    } else {
                      echo '&nbsp;As directed by<br>&nbsp;your provider:<br><br>&nbsp;______________';
                    }
                    ?>
                  </td>
                </tr>
                <?php
              }
              $p++;
            }
            $tmp = $key;
          }
          ?>

        </table>

      </td>
    </tr>
  </table>
  <?php
}
?>

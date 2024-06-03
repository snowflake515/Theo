<?php
$dt = $this->EncounterHistoryModel->get_by_id($id)->row();
$Encounter_Id = $id;
$ProviderKey = $dt->Provider_ID;
$PatientKey = $dt->Patient_ID;
$Dept_Id =  ($dt->Dept_ID) ? (int)$dt->Dept_ID : @(int)$current_user->Dept_Id;;

$DeptKey = $Dept_Id;
$ConfigKey  = $ConfigKey; 

$SupStruct = array();
$RendStruct = array(
    'FirstName' => "",
    'MiddleName' => "",
    'LastName' => "",
    'Title' => "",
    'Suffix' => "",
    'Addr1' => "",
    'Addr2' => "",
    'City' => "",
    'State' => "",
    'Zip' => "",
    'Phone' => "",
    'Fax' => "",
);

if ($ProviderKey == 0 || $ProviderKey == 1) {
  $sql = "	Select TOP 1
	       P.ProviderFirstName,
           P.ProviderMiddleName,
	       P.ProviderLastName,
	       P.ProviderTitle,
	       P.ProviderSuffix,
	       P.ProviderAddress1,
	       P.ProviderAddress2,
	       P.ProviderCity,
	       P.ProviderState,
	       P.ProviderZip,
	       P.ProviderPhone,
	       P.ProviderFax,
		   P.Provider_Id
	  From " . $data_db . ".dbo.ProviderProfile P,
	       " . $data_db . ".dbo.PatientProfile PP
     Where PP.Patient_Id=$PatientKey
	   And P.Provider_Id=PP.Provider_Id";

  $NoProvider = $this->ReportModel->data_db->query($sql);
  $NoProvider_num = $NoProvider->num_rows();
  $NoProvider_row = $NoProvider->row();

  $RendStruct = array(
      'FirstName' => $NoProvider_row->ProviderFirstName,
      'MiddleName' => $NoProvider_row->ProviderMiddleName,
      'LastName' => $NoProvider_row->ProviderLastName,
      'Title' => $NoProvider_row->ProviderTitle,
      'Suffix' => $NoProvider_row->ProviderSuffix,
      'Addr1' => $NoProvider_row->ProviderAddress1,
      'Addr2' => $NoProvider_row->ProviderAddress2,
      'City' => $NoProvider_row->ProviderCity,
      'State' => $NoProvider_row->ProviderState,
      'Zip' => $NoProvider_row->ProviderZip,
      'Phone' => $NoProvider_row->ProviderPhone,
      'Fax' => $NoProvider_row->ProviderFax,
  );
}

$sql = "Select TOP 1
       LogoPosition,
       BlockInfoPosition,
       PatientName,
	   PatientAddress,
	   PatientCity,
	   PatientState,
	   PatientZip,
	   AccountNumber,
	   MRN,
	   DatePrinted,
	   PrintedPatientName,
	   PatientSSN,
	   PatientDOB,
	   PatientAge,
	   Provider,
	   isnull(SupervisingProvider, Provider) as SupervisingProvider,
	   EncounterDate,
	   Margin1,
	   Margin2,
	   Dictated,
	   isnull(OptionAlignment, 0) as OptionAlignment
  From " . $data_db . ".dbo.EncounterConfig
 Where EncounterConfig_Id=$ConfigKey";

$CustomConfig = $this->ReportModel->data_db->query($sql);
$CustomConfig_num = $CustomConfig->num_rows();
$CustomConfig_row = $CustomConfig->row();

$data['data_db'] = $data_db;
$BodyFontInfo = getChartHeaderFontInfo($data, $ConfigKey);
$DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . "sans-serif" . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
$LargerStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . ($BodyFontInfo['FontSize'] + 4) . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . "sans-serif" . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";

$sql = "Select TOP 1
       ImageType
  From " . $image_db . ".dbo.AdminImages
 Where Dept_Id=$DeptKey";

$GetDeptType = $this->ReportModel->data_db->query($sql);
$GetDeptType_num = $GetDeptType->num_rows();
$GetDeptType_row = $GetDeptType->row();

$sql= "Select * From $image_db.dbo.AdminImages Where Dept_Id=$DeptKey";
$logo = $this->ReportModel->data_db->query($sql);
$logo_num = $logo->num_rows();
$logo_row = $logo->row();

$sql = "Select TOP 1
       E.EncounterNotes,
	   E.EncounterDate,
	   E.Dept_Id,
	   E.Provider_Id,
	   E.SupProvider_Id,
	   E.EncounterSignedOff,
	   E.ChiefComplaint,
	   D.EncounterDescription,
	   D.EncounterDescription_Id,
	   F.FacilityName,
	   PP.FirstName,
	   PP.MiddleName,
	   PP.LastName,
	   PP.SSN,
   	   PP.Sex,
	   PP.DOB,
	   PP.MedicalRecordNumber,
	   PP.AccountNumber,
	   PP.Addr1,
	   PP.Addr2,
	   PP.City,
	   PP.State,
	   PP.Zip,
	   PL.ProblemDescription
  From " . $data_db . ".dbo.EncounterHistory E
  Left Outer Join " . $data_db . ".dbo.EncounterDescriptionList D
    On E.EncounterDescription_Id=D.EncounterDescription_Id
  Join " . $data_db . ".dbo.PatientProfile PP
    On E.Patient_Id=PP.Patient_Id
  Left Outer Join " . $data_db . ".dbo.FacilityList F
    On E.Facility_Id=F.Facility_Id
  Left Outer Join " . $data_db . ".dbo.ProblemList PL
    On E.Problem_Id=PL.Problem_Id
 Where E.Encounter_Id=$Encounter_Id";

$HeaderData = $this->ReportModel->data_db->query($sql);
$HeaderData_num = $HeaderData->num_rows();
$HeaderData_row = $HeaderData->row();

if ($HeaderData_num != 0) {
  $Provider_Id = $HeaderData_row->Provider_Id;
} else {
  $Provider_Id = 0;
}

if ($HeaderData_row->SupProvider_Id >= 1) {
  $SupProvider_Id = $HeaderData_row->SupProvider_Id;
} else {
  $SupProvider_Id = 0;
}

$sql = "Select TOP 2
       ProviderFirstName,
       ProviderMiddleName,
       ProviderLastName,
       ProviderTitle,
       ProviderSuffix,
       ProviderAddress1,
       ProviderAddress2,
       ProviderCity,
       ProviderState,
       ProviderZip,
       ProviderPhone,
       ProviderFax,
       Provider_Id
  From " . $data_db . ".dbo.ProviderProfile
 Where (Provider_Id=$Provider_Id OR Provider_Id=$SupProvider_Id)";

$SupProvider = $this->ReportModel->data_db->query($sql);
$SupProvider_num = $SupProvider->num_rows();
$SupProvider_result = $SupProvider->result();


foreach ($SupProvider_result as $spr) {
  if ($HeaderData_row->SupProvider_Id == $spr->Provider_Id) {
    $SupStruct['FirstName'] = $spr->ProviderFirstName;
    $SupStruct['MiddleName'] = $spr->ProviderMiddleName;
    $SupStruct['LastName'] = $spr->ProviderLastName;
    $SupStruct['Title'] = $spr->ProviderTitle;
    $SupStruct['Suffix'] = $spr->ProviderSuffix;
    $SupStruct['Addr1'] = $spr->ProviderAddress1;
    $SupStruct['Addr2'] = $spr->ProviderAddress2;
    $SupStruct['City'] = $spr->ProviderCity;
    $SupStruct['State'] = $spr->ProviderState;
    $SupStruct['Zip'] = $spr->ProviderZip;
    $SupStruct['Phone'] = $spr->ProviderPhone;
    $SupStruct['Fax'] = $spr->ProviderFax;
  }

  if ($HeaderData_row->Provider_Id == $spr->Provider_Id) {
    if ($ProviderKey != 0 && $ProviderKey != 1) {
      $RendStruct['FirstName'] = $spr->ProviderFirstName;
      $RendStruct['MiddleName'] = $spr->ProviderMiddleName;
      $RendStruct['LastName'] = $spr->ProviderLastName;
      $RendStruct['Title'] = $spr->ProviderTitle;
      $RendStruct['Suffix'] = $spr->ProviderSuffix;
      $RendStruct['Addr1'] = $spr->ProviderAddress1;
      $RendStruct['Addr2'] = $spr->ProviderAddress2;
      $RendStruct['City'] = $spr->ProviderCity;
      $RendStruct['State'] = $spr->ProviderState;
      $RendStruct['Zip'] = $spr->ProviderZip;
      $RendStruct['Phone'] = $spr->ProviderPhone;
      $RendStruct['Fax'] = $spr->ProviderFax;
    }
  }
}

if ($CustomConfig_row->LogoPosition == 0) {
  $CustomConfig_LogoPosition = 4;
}
if ($CustomConfig_row->BlockInfoPosition == 0) {
  $CustomConfig_BlockInfoPosition = 4;
}


?>
<span style="<?php echo $DefaultStyle; ?>">
<h1 style="color: #35A7CF">Annual Personalized Wellness Plan</h1>
</span>



<table border="0" cellpadding="3" cellspacing="0" style="width: 7.5in; padding-right: 13px;">

	<tr>
    <?php
    if($CustomConfig_row->OptionAlignment == 0){
      echo '<td nowrap align="left" valign="top">';
    ?>

  <table cellpadding="1" cellspacing="0">
      <?php
      $NeedLineBreaks = 0;
      $DisplayInfo = "";
      if($HeaderData_row->FirstName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."".$HeaderData_row->FirstName;
        $NeedLineBreaks = 1;
      }

      if($HeaderData_row->MiddleName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->MiddleName;
        $NeedLineBreaks = 1;
      }

      if($CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->LastName."<br/>";
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Addr1 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr1."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->Addr2 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr2."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->City != "" && $CustomConfig_row->PatientCity != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->City;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->State != "" && $CustomConfig_row->PatientState != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->State;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Zip != "" && $CustomConfig_row->PatientZip != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Zip;
        $NeedLineBreaks = 1;
      }

      if(trim($DisplayInfo) != ""){
      ?>
      <?php
      }
      // if($CustomConfig_row->AccountNumber != 0){
      ?>	
      
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?> font-size: 13px" valign="top">
            Patient Name: <b><?php echo $HeaderData_row->FirstName." ".$HeaderData_row->MiddleName." ".$HeaderData_row->LastName; ?></b>
          </td>
        </tr>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?> font-size: 13px" valign="top">
            Date: <b><?php echo date('F d, Y', strtotime($HeaderData_row->EncounterDate)); ?></b>
          </td>
        </tr>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?> font-size: 13px" valign="top">
            Healthcare Provider: <b>
                <?php
                echo trim($RendStruct['Title'])." ".$RendStruct['FirstName']." ".$RendStruct['MiddleName']." ".$RendStruct['LastName'];
                if($RendStruct['Suffix'] != ""){
                  echo ",";
                  echo trim($RendStruct['Suffix']);
                }
              ?></b>
          </td>
        </tr>
    </table>

    <?php
      echo '</td>';
      echo '<td nowrap align="right" valign="top">';
    ?>
      <table border="0" cellpadding="1" cellspacing="0">
        <!-- <?php
        if($CustomConfig_row->DatePrinted != 0){
        ?> -->
        <!-- <?php
        }
        if($CustomConfig_row->PrintedPatientName != 0){
        ?> -->
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?> font-size: 13px" valign="top">
             MRN:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?> font-size: 13px" valign="top">
              <strong><?php echo $HeaderData_row->MedicalRecordNumber; ?></strong>
            </td>
          </tr>
        <!-- <?php
        }
        if($CustomConfig_row->PatientSSN != 0){
        ?> -->
        <!-- <?php
        }
        if($CustomConfig_row->PatientDOB != 0){
        ?> -->
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?> font-size: 13px" valign="top">
              Practice:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?> font-size: 13px" valign="top">
              <strong>
                  New River Family Medicine <br>
                  1300 St Marys Street Suite 502 R
            </strong>
            </td>
          </tr>
        <!-- <?php
        }
        if($CustomConfig_row->PatientAge != 0){
        ?> -->
        <!-- <?php
        }
        if($CustomConfig_row->Provider != 0){
        ?> -->
        <!-- <?php
        }
        if($CustomConfig_row->SupervisingProvider != "" && $HeaderData_row->SupProvider_Id > 0 && $HeaderData_row->Provider_Id > 1 && $HeaderData_row->SupProvider_Id != $HeaderData_row->Provider_Id){
        ?> -->
       <!-- <?php
        }
       ?> -->

      </table>
    <?php
      echo '</td>';
    }else{
    ?>

    <?php
    ?>
    <?php
    }
    ?>
	</tr>
  <tr>
    <td align="center" colspan="2">
      <hr noshade size="8" color="#35A7CF">
    </td>
  </tr>
  <tr style="background-color: #DDEEF3;">
    <td align="left" colspan="2">
      <strong style="font-size: 13px; font-style: italic; color: #35A7CF; font-family: sans-serif;">
        Your personallized Wellness Plan is based on risk factors identified during your Annual Wellness Visit. Work with your doctor to complete each of the identified screenings and recommended counseling over the next 12 months to ensure you stay as healthy as possible. Speak with your Doctor if you have any questions about this plan.
      </strong>
    </td>
  </tr>
</table>

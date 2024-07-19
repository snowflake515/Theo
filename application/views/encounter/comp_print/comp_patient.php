<?php
$sql1 = "
DELETE FROM Wellness_DataArchive.dbo.PWHistory

USE Wellness_eCastEMR_Data
DROP Table Wellness_eCastEMR_Data.dbo.tempPWTBots
SET ANSI_NULLS ON
SET QUOTED_IDENTIFIER ON
SET ANSI_PADDING ON
CREATE TABLE Wellness_eCastEMR_Data.[dbo].[tempPWTBots](
	[tempPWTBots_ID] [int] IDENTITY(1,1) NOT NULL,
	[TBot] varchar(10) )
CREATE INDEX tempPWTBots_idx
  ON Wellness_eCastEMR_Data.dbo.tempPWTBots(TBot)

SET ANSI_PADDING OFF

DECLARE @Patient_ID INT, @Encounter_ID INT, @n INT, @max INT, 
        @PWCategory Varchar(50), @PWService Varchar(50), @PWCode Varchar(10),@PWBenefit Varchar(50),@PWNeeded SmallInt,
		@PWMaster_ID INT, @TDate DATE, @PWSortOrder SMALLINT
SELECT @Encounter_ID	= $PrimaryKey
SELECT @Patient_ID		= $PatientKey

UPDATE Wellness_DataArchive.dbo.PWHistory
SET Hidden		= 1 WHERE 
Patient_ID		= @Patient_ID AND 
Encounter_ID	= @Encounter_ID

SELECT @TDate	= GetDate()  

PRINT 'Inserting first 4 rows of PWHistory...'
SELECT * FROM Wellness_DataArchive.dbo.PWHistory
DELETE FROM Wellness_DataArchive.dbo.PWHistory
INSERT INTO Wellness_DataArchive.dbo.PWHistory
(PWMaster_ID, Patient_ID, Encounter_ID, PWDate, PWNeeded, PWValue, SortOrder, Hidden)
VALUES
(1,@Patient_ID,@Encounter_ID,@TDate,0,'Weight:    ',10,0),
(2,@Patient_ID,@Encounter_ID,@TDate,0,'Height:    ',20,0),
(3,@Patient_ID,@Encounter_ID,@TDate,0,'Systolic:  ',30,0),
(4,@Patient_ID,@Encounter_ID,@TDate,0,'Diastolic: ',40,0)
PRINT 'Now updating first 4 rows of PWHistory with correct vitals values...'

UPDATE PWH
SET PWH.PWValue = E3I.ETL3Input
FROM Wellness_DataArchive.dbo.PWHistory PWH
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL3Input E3I ON PWH.Encounter_ID = E3I.Encounter_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML3 T3 ON E3I.TML3_ID = T3.TML3_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML2 T2 ON T3.TML2_ID = T2.TML2_ID
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL1 E1 ON T2.TML1_ID = E1.TML1_ID
WHERE PWH.PWMaster_ID = 1
AND E1.Encounter_ID = @Encounter_ID
AND T2.TML2_HeaderMaster_ID = 33
AND T3.TML3_TBotMaster_ID = 424;

UPDATE PWH
SET PWH.PWValue = E3I.ETL3Input
FROM Wellness_DataArchive.dbo.PWHistory PWH
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL3Input E3I ON PWH.Encounter_ID = E3I.Encounter_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML3 T3 ON E3I.TML3_ID = T3.TML3_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML2 T2 ON T3.TML2_ID = T2.TML2_ID
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL1 E1 ON T2.TML1_ID = E1.TML1_ID
WHERE PWH.PWMaster_ID = 2
AND E1.Encounter_ID = @Encounter_ID
AND T2.TML2_HeaderMaster_ID = 33
AND T3.TML3_TBotMaster_ID = 423;

UPDATE PWH
SET PWH.PWValue = E3I.ETL3Input
FROM Wellness_DataArchive.dbo.PWHistory PWH
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL3Input E3I ON PWH.Encounter_ID = E3I.Encounter_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML3 T3 ON E3I.TML3_ID = T3.TML3_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML2 T2 ON T3.TML2_ID = T2.TML2_ID
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL1 E1 ON T2.TML1_ID = E1.TML1_ID
WHERE PWH.PWMaster_ID = 3
AND E1.Encounter_ID = @Encounter_ID
AND T2.TML2_HeaderMaster_ID = 33
AND T3.TML3_TBotMaster_ID = 425;

UPDATE PWH
SET PWH.PWValue = E3I.ETL3Input
FROM Wellness_DataArchive.dbo.PWHistory PWH
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL3Input E3I ON PWH.Encounter_ID = E3I.Encounter_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML3 T3 ON E3I.TML3_ID = T3.TML3_ID
INNER JOIN Wellness_eCastEMR_Template.dbo.TML2 T2 ON T3.TML2_ID = T2.TML2_ID
INNER JOIN Wellness_eCastEMR_Data.dbo.ETL1 E1 ON T2.TML1_ID = E1.TML1_ID
WHERE PWH.PWMaster_ID = 4
AND E1.Encounter_ID = @Encounter_ID
AND T2.TML2_HeaderMaster_ID = 33
AND T3.TML3_TBotMaster_ID = 426;

INSERT INTO Wellness_eCastEMR_Data.[dbo].[tempPWTBots]
SELECT CONCAT(TM.TML3_TBotMaster_ID,'-',TM.TML3_TBotData) FROM Wellness_eCastEMR_Data.dbo.ETL3 ET
JOIN Wellness_eCastEMR_Template.dbo.TML3 TM ON ET.TML3_ID = TM.TML3_ID
JOIN eCastMaster.dbo.TBotMaster TB ON TM.TML3_TBotMaster_ID = TB.TBotMaster_ID
WHERE ET.Encounter_ID	= @Encounter_ID

SELECT @n = 5
SELECT @max = COUNT(*) FROM Wellness_DataArchive.dbo.PWMaster
WHILE @n <= @max
  BEGIN
	  SELECT  
	    @PWMaster_ID		= PWMaster_ID,
      @PWCategory			= Category,
      @PWService			= Service,
      @PWCode 			  = Code,
      @PWBenefit			= Benefit,
      @PWNeeded			  = 0,
      @PWSortOrder		= SortOrder
		FROM Wellness_DataArchive.dbo.PWMaster WHERE PWMaster_ID = @n
	  SELECT TBot FROM Wellness_DataArchive.dbo.PWMasterTBots TB WHERE TB.PWMaster_ID = @n
      INTERSECT
    SELECT TBot FROM Wellness_eCastEMR_Data.dbo.TempPWTBots TTB
		IF @@ROWCOUNT <> 0 
		  BEGIN 
		    SELECT @PWNeeded = 1
		  END
    INSERT INTO Wellness_DataArchive.dbo.PWHistory
    (PWMaster_ID,Patient_ID,Encounter_ID,PWDate,PWNeeded,PWValue,SortOrder,Hidden)
    VALUES
    (@PWMaster_ID,@Patient_ID,@Encounter_ID,@TDate,@PWNeeded,'',@PWSortOrder,0)
    SELECT @n = @n + 1 
  END
";

$sql = "	SELECT PWM.PWMaster_ID,PWM.Category,PWM.Service,PWM.Code,PWM.Benefit,PWH.PWValue,PWH.PWNeeded,PWM.SortOrder
FROM Wellness_DataArchive.dbo.PWHistory PWH
JOIN Wellness_DataArchive.dbo.PWMaster PWM
ON PWH.PWMaster_ID = PWM.PWMaster_ID
WHERE Patient_ID = $PatientKey AND Encounter_ID = $PrimaryKey AND
(PWH.Hidden IS NULL or PWH.Hidden = 0)
ORDER BY PWH.SortOrder";

$sql2 = "SELECT DOB FROM Wellness_eCastEMR_Data.dbo.PatientProfile where Patient_ID = $PatientKey";

  $res = $this->ReportModel->data_db->query($sql1);
  $this->ReportModel->data_db->close();
  $getAWACSScreening = $this->ReportModel->data_db->query($sql);


  log_message('error', "ididid==>>>");
  log_message('error', $PatientKey);
  log_message('error', $PrimaryKey);

$getAWACSScreening_num = $getAWACSScreening->num_rows();
$getAWACSScreening_result = $getAWACSScreening->result();
$this->ReportModel->data_db->close();

$dob = $this->ReportModel->data_db->query($sql2);
$dob_result = $dob->row();

if ($getAWACSScreening_num != 0) {

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 11  " .  "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . "sans-serif" . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 12 " .  "px; font-weight: bold; font-family: " . "sans-serif" . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px;">
    <tr>
      <tr>
        <td>
          <table border="0" cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px; border-style:solid; border-collapse:collapse; border-width:1px; border-top: none; border-left: none; border-right: none; border-color: #999999; border-spacing:2px;">
              <tr>
                <td nowrap align="left" colspan="4" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-left: none; border-top: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                  <label style="font-size: 14px; font-weight: bolder;">Your Key Vital Signs</label>
                </td>
              </tr>
              <tr style="width: 22px;">
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; width: 25%; border-width:1px; border-bottom: none; border-right: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">Age: &nbsp;</label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; width: 20%; text-decoration: underline; border-right: none; border-left: none; border-bottom: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;"><?php echo dob_to_age($dob_result->DOB);?></label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px;  border-right: none; width: 18%; border-left: none; border-bottom: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">Blood Pressure: &nbsp;</label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; text-decoration: underline; border-bottom: none; border-left: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">
                    <?php 
                      if (is_numeric($getAWACSScreening_result[2]->PWValue) && is_numeric($getAWACSScreening_result[3]->PWValue)) {
                        echo $getAWACSScreening_result[2]->PWValue; ?>&nbsp;/<?php echo $getAWACSScreening_result[3]->PWValue; 
                      }else{
                        echo "N/A";
                      }
                    ?>
                  </label>
                </td>
              </tr>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; width: 25%;  border-width:1px; border-bottom: none; border-top: none; border-right: none; padding:2px;" valign="center">
                  <label style="font-size: 13.5px;">Weight: &nbsp;</label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; text-decoration: underline; width: 20%; border-width:0px; border: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">
                    <?php 
                      if (is_numeric($getAWACSScreening_result[0]->PWValue) && intval($getAWACSScreening_result[0]->PWValue) != 0) {
                        echo $getAWACSScreening_result[0]->PWValue . " lbs";
                      }else{
                        echo "N/A";
                      }
                    ?>&nbsp;
                  </label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border: none; width: 17%; none;padding:2px;" valign="top">
                  
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-bottom: none; border-top: none; border-left: none; padding:2px;" valign="top">
                  
                </td>
              </tr>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; width: 25%;  border-width:1px; border-bottom: none; border-top: none; border-right: none;padding:2px;" valign="center">
                  <label style="font-size: 13.5px;">Height: &nbsp;</label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; text-decoration: underline; width: 20%; border-width:1px; border: none; padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">
                    <?php 
                      if (is_numeric($getAWACSScreening_result[1]->PWValue) && intval($getAWACSScreening_result[1]->PWValue) != 0) {
                        echo $getAWACSScreening_result[1]->PWValue . " inches";
                      }else{
                        echo "N/A";
                      }
                    ?>&nbsp;
                  </label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border: none; width: 17%; none;padding:2px;" valign="top">
                  
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-bottom: none; border-top: none; border-left: none; padding:2px;" valign="top">
                  
                </td>
              </tr>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; width: 25%;  border-width:1px; border-bottom: none; border-top: none; border-right: none;padding:2px;" valign="center">
                  <label style="font-size: 13.5px;">Body Mass Index (BMI): &nbsp;</label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; text-decoration: underline; width: 20%; border-width:1px; border: none; none;padding:2px;" valign="top">
                  <label style="font-size: 13.5px;">
                    <?php 
                    if (!is_numeric($getAWACSScreening_result[0]->PWValue) || intval($getAWACSScreening_result[0]->PWValue) == 0 || !is_numeric($getAWACSScreening_result[1]->PWValue) || intval($getAWACSScreening_result[1]->PWValue) == 0) {
                      echo "N/A";
                    }else{
                      echo intval($getAWACSScreening_result[0]->PWValue / ($getAWACSScreening_result[1]->PWValue * $getAWACSScreening_result[1]->PWValue) * 703); 
                    }
                    ?>&nbsp;
                  </label>
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border: none; width: 17%; none;padding:2px;" valign="top">
                  
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-bottom: none; border-top: none; border-left: none; padding:2px;" valign="top">
                  
                </td>
              </tr>
          </table>

          <?php foreach ($getAWACSScreening_result as $index => $val) { ?>
            <?php if ($index == 4) { ?>
              <table border="0" cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px; border-style:solid; border-collapse:collapse; border-width:1px; border-top: none; border-left: none; border-right: none; border-color: #999999; border-spacing:2px;">
                <tr>
                  <td nowrap align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Medicare Recommended</label>
                  </td>
                  <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Services</label>
                  </td>
                  <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Code</label>
                  </td>
                  <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Your Benefit/Guidelines</label>
                  </td>
                  <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Needed</label>
                  </td>
                  <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-top: none; border-left: none; border-right: none; padding:2px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;"></label>
                  </td>
                </tr>
            <?php } ?>
            <?php if ($index == 15) { ?>
              <!-- <table border="0" cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px; margin-bottom: 10px; border-style:solid; border-collapse:collapse; border-width:1px; border-top: none; border-left: none; border-right: none; border-color: #999999; border-spacing:2px;"> -->
                <tr>
                  <td nowrap align="left" colspan="6" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-left: none; border-right: none; padding-top:5px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Social/Behavioral Screenings</label>
                  </td>
                </tr>
            <?php } ?>
            <?php if ($index == 20) { ?>
              <!-- <table border="0" cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px; margin-bottom: 10px; border-style:solid; border-collapse:collapse; border-width:1px; border-top: none; border-left: none; border-right: none; border-color: #999999; border-spacing:2px;"> -->
                <tr>
                  <td nowrap align="left" colspan="6" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-left: none; border-right: none; padding-top:5px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Your Additional Risk Factors</label>
                  </td>
                </tr>
            <?php } ?>
            <?php if ($index == 24) { ?>
              <!-- <table border="0" cellpadding="0" cellspacing="0" style="width: 7.5in; padding-right: 13px; margin-bottom: 10px; border-style:solid; border-collapse:collapse; border-width:1px; border-top: none; border-left: none; border-right: none; border-color: #999999; border-spacing:2px;"> -->
                <tr>
                  <td nowrap align="left" colspan="6" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:0px; border-left: none; border-right: none; padding-top:5px; color: #35A7CF" valign="top">
                    <label style="font-size: 14px; font-weight: bolder;">Advance Care Planning</label>
                  </td>
                </tr>
            <?php } ?>
               <?php if ($index >= 4 && $index < 25) { ?>
                <tr>
                  <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-right: none;padding:2px;" valign="center">
                    <label style="font-size: 13.5px;"><?php echo $val->Category; ?>&nbsp;</label>
                  </td>
                  <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-left: none; border-right: none;padding:2px;" valign="center">
                    <label style="font-size: 13.5px;"><?php echo $val->Service; ?>&nbsp;</label>
                  </td>
                  <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-left: none; border-right: none;padding:2px;" valign="center">
                    <label style="font-size: 13.5px;"><?php echo $val->Code; ?>&nbsp;</label>
                  </td>
                  <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-left: none; border-right: none;padding:2px;" valign="center">
                    <label style="font-size: 13.5px;"><?php echo $val->Benefit; ?>&nbsp;</label>
                  </td>
                  <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-left: none; border-right: none; padding:2px; width: 59px;" valign="center">
                    <?php 
                    if ($val->Category == "Weight/BMI") {
                      if ((is_numeric($getAWACSScreening_result[0]->PWValue) && intval($getAWACSScreening_result[0]->PWValue) != 0 && is_numeric($getAWACSScreening_result[1]->PWValue) && intval($getAWACSScreening_result[1]->PWValue) != 0)) {
                        if (($getAWACSScreening_result[0]->PWValue / ($getAWACSScreening_result[1]->PWValue * $getAWACSScreening_result[1]->PWValue) * 703) >= 25) {
                          echo '<label style="font-size: 13.5px;">Yes<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false" checked></label>';
                        }else{
                          echo '<label style="font-size: 13.5px;">Yes<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                        }
                      }else{
                        echo '<label style="font-size: 13.5px;">Yes<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                      } 
                    }else{
                      if ($val->PWNeeded === '1') {
                        echo '<label style="font-size: 13.5px;">Yes<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false" checked></label>';
                      }else{
                        echo '<label style="font-size: 13.5px;">Yes<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                      }
                    }                   
                    ?>&nbsp;
                  </td>
                  <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:1px; border-left: none; padding:0px; width: 59px; font-family: fantasy; " valign="center">
                    <?php 
                    if ($val->Category == "Weight/BMI") {
                      if ((is_numeric($getAWACSScreening_result[0]->PWValue) && intval($getAWACSScreening_result[0]->PWValue) != 0 && is_numeric($getAWACSScreening_result[1]->PWValue) && intval($getAWACSScreening_result[1]->PWValue) != 0)) {
                        if (($getAWACSScreening_result[0]->PWValue / ($getAWACSScreening_result[1]->PWValue * $getAWACSScreening_result[1]->PWValue) * 703) < 25) {
                          echo ' <label style="font-family: sans-serif; font-size: 13.5px;">No<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false" checked></label>';
                        }else{
                          echo ' <label style="font-family: sans-serif; font-size: 13.5px;">No<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                        }
                      }else{
                        echo ' <label style="font-family: sans-serif; font-size: 13.5px;">No<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                      }  
                    }else{
                      if ($val->PWNeeded === '0') {
                        echo ' <label style="font-family: sans-serif; font-size: 13.5px;">No<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false" checked></label>';
                      }else{
                        echo ' <label style="font-family: sans-serif; font-size: 13.5px;">No<input type="checkbox" class="checkBox" style="width: 13.5px; height: 13.5px;" onclick="return false"></label>';
                      }
                    }                 
                    ?>&nbsp;
                  </td>
                </tr>
              <?php } ?>  
      <?php } ?>
            </table> 
          </table>  
    <?php
}
?>

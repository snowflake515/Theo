<?php
$sql1 = "USE Wellness_eCastEMR_Data
DROP Table Wellness_eCastEMR_Data.dbo.tempTBots
SET ANSI_NULLS ON
SET QUOTED_IDENTIFIER ON
SET ANSI_PADDING ON
CREATE TABLE Wellness_eCastEMR_Data.[dbo].[tempTBots](
  [tempTBots_ID] [int] IDENTITY(1,1) NOT NULL,
  [TBot] varchar(10) )
CREATE INDEX tempTBots_idx
  ON Wellness_eCastEMR_Data.dbo.tempTBots(TBot)

SET ANSI_PADDING OFF
DECLARE @Patient_ID INT, @Encounter_ID INT, @n INT, @max INT, 
        @CTrackName Varchar(50), @CTrackQty Varchar(50), @CTrackFreq Varchar(50),
    @CTrackMaster_ID INT, @TDate DATE, @SortOrder SMALLINT

SELECT @Encounter_ID	= $PrimaryKey
SELECT @Patient_ID		= $PatientKey

UPDATE Wellness_DataArchive.dbo.CTracksHistory
SET Hidden		= 1 WHERE 
Patient_ID		= @Patient_ID AND 
Encounter_ID	= @Encounter_ID

SELECT @TDate			= GetDate() 
INSERT INTO Wellness_eCastEMR_Data.[dbo].[tempTBots]
SELECT CONCAT(TM.TML3_TBotMaster_ID,'-',TM.TML3_TBotData) FROM Wellness_eCastEMR_Data.dbo.ETL3 ET
JOIN Wellness_eCastEMR_Template.dbo.TML3 TM ON ET.TML3_ID = TM.TML3_ID
JOIN eCastMaster.dbo.TBotMaster TB ON TM.TML3_TBotMaster_ID = TB.TBotMaster_ID
WHERE ET.Encounter_ID	= @Encounter_ID

SELECT @n = 1
SELECT @max = COUNT(*) FROM Wellness_DataArchive.dbo.CTracksMaster
WHILE @n <= @max
  BEGIN
    SELECT  
      @CTrackMaster_ID	= CTrackMaster_ID,
    @CTrackName			= CTrackName,
    @CTrackQty			= CTrackQty,
    @CTrackFreq			= CTrackFreq,
    @SortOrder			= SortOrder
    FROM Wellness_DataArchive.dbo.CTracksMaster WHERE CTrackMaster_ID = @n
      SELECT TBot FROM Wellness_DataArchive.dbo.CTracksMasterTBots TB WHERE TB.CTracksMaster_ID = @n
        INTERSECT
        SELECT TBot FROM Wellness_eCastEMR_Data.dbo.TempTBots TTB
    IF @@ROWCOUNT <> 0
      BEGIN
      INSERT INTO Wellness_DataArchive.dbo.CTracksHistory
      (CTrackMaster_ID,Patient_ID,Encounter_ID,CTrackDate,CTrackQty,CTrackFreq,SortOrder,Hidden)
      VALUES
      (@CTrackMaster_ID,@Patient_ID,@Encounter_ID,@TDate,@CTrackQty,@CTrackFreq,@SortOrder,0)
      END
    SELECT @n = @n + 1
  END
  ";

$sql = "	SELECT CTM.CTrackMaster_ID,CTM.CTrackName,CTH.CTrackQty,CTH.CTrackFreq,CTH.SortOrder
FROM Wellness_DataArchive.dbo.CTracksHistory CTH
JOIN Wellness_DataArchive.dbo.CTracksMaster CTM
ON CTH.CTrackMaster_ID = CTM.CTrackMaster_ID
WHERE Patient_ID = $PatientKey AND Encounter_ID = $PrimaryKey AND 
(CTH.Hidden IS NULL or CTH.Hidden = 0)
ORDER BY CTH.SortOrder";

  $res = $this->ReportModel->data_db->query($sql1);
  $this->ReportModel->data_db->close();
  // $res->result();
  $getAWACSScreening = $this->ReportModel->data_db->query($sql);

// $res = $this->db->query($sql1, false);
// $result = odbc_exec($connection, $query);
// odbc_free_result();
// $res = $this->db->query($sql, false);
// // $res = $this->db->query($sql1 . $sql, TRUE);
// log_message('error', "res");
// // log_message('error', $res);
// $methods = get_class_methods($getAWACSScreening);

// foreach ($methods as $method) {
//   log_message("error", $method);
//   log_message('error', json_encode($getAWACSScreening->$method()));
// }

// $getAWACSScreening = $this->ReportModel->data_db->query($sql1 . $sql);

// $methods = get_class_methods($res);

// foreach ($methods as $method) {
//   log_message("error", $method);
//   log_message('error', json_encode($res->$method()));
  
// }

$getAWACSScreening_num = $getAWACSScreening->num_rows();
$getAWACSScreening_result = $getAWACSScreening->result();
log_message('error', "=-----------------------------------------Clinical Tracks OKOKOK");
log_message('error', $getAWACSScreening_num);
if ($getAWACSScreening_num != 0) {

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 14  " .  "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 14 " .  "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
    <cfoutput>
      </tr>
      <tr>
        <td width="7">&nbsp;</td>
        <td>
          <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
            <tr>
              <td nowrap align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                Clinical Track Component 
              </td>
              <?php
              ?>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                Frequency 
              </td>
            </tr>

            <?php foreach ($getAWACSScreening_result as $val) { ?>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                  <?php echo $val->CTrackName; ?>&nbsp;
                </td>
                <?php
                ?>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->CTrackFreq; ?>&nbsp;
                </td>
              </tr>
            <?php } ?>

          </table>
          <?php
    }
?>

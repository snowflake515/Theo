<?php 
//
//<!--- Program: comp_vitals.cfm
//
//		JWY 4/1/08 - Fix BMI calculation.  Metric.
//--->
//<cfinclude template="VitalsConversion.cfm">
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sUTC_DST=Session.UTC_DST>
//	<cfset Variables.sUTC_TimeOffset=Session.UTC_TimeOffset>
//</cflock>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="TempOrg">
//	Select TOP 1
//		Org_Id
//	From PatientProfile
//	Where Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PatientKey#">
//</cfquery>
//
//
$sql = "	Select TOP 1
		Org_Id
	From ".$data_db.".dbo.PatientProfile
	Where Patient_Id= $PatientKey";

$TempOrg = $this->ReportModel->data_db->query($sql);
$TempOrg_num = $TempOrg->num_rows();
$TempOrg_result = $TempOrg->result();
$TempOrg_row = $TempOrg->row();

//
//<cfquery datasource="#Attributes.EMRDataSource#" name="VitalsUnitsConfig">
//	Select TOP 1
//		isnull(EnglishMetric, 0) as EnglishMetric
//	From OrgProfile
//	Where Org_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#TempOrg.Org_Id#">
//</cfquery>
//
//

$sql = "	Select TOP 1
		isnull(EnglishMetric, 0) as EnglishMetric
	From ".$data_db.".dbo.OrgProfile
	Where Org_Id= $TempOrg_row->Org_Id";

$VitalsUnitsConfig = $this->ReportModel->data_db->query($sql);
$VitalsUnitsConfig_num = $VitalsUnitsConfig->num_rows();
$VitalsUnitsConfig_row = $VitalsUnitsConfig->row();


//
//<cfquery datasource="#Attributes.EMRDataSource#" name="VitalNotes">
//	Select 
//		dbo.UTCtoLocal(V.EncounterDate_UTC, <cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#variables.sUTC_TimeOffset#">, <cfqueryparam cfsqltype="CF_SQL_BIT" value="#variables.sUTC_DST#">) as EncounterDate,
//		V.LMP,
//		V.ExpectedDueDate,
//		V.Temperature_Celsius, 
//		V.Respiration_breathspermin,
//		V.Waist_cm, 
//		V.Weight_Kg, 
//		V.Height_cm, 
//		V.Pulse_beatspermin, 
//		V.O2_Saturation_percent, 
//		V.Systolic_mmHg, 
//		V.Diastolic_mmHg, 
//		V.Head_Circumference_cm, 
//		V.VitalNotes,
//		P.Sex
//	From VitalsHistory V,
//		PatientProfile P
//	Where V.VitalsHistory_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And V.Patient_Id=P.Patient_Id
//		And (V.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR V.Hidden IS NULL)
//	Order BY V.EncounterDate_UTC DESC, V.VitalsHistory_Id DESC
//</cfquery>
//
//

// yobi skip
// dbo.UTCtoLocal(V.EncounterDate_UTC, <cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#variables.sUTC_TimeOffset#">, <cfqueryparam cfsqltype="CF_SQL_BIT" value="#variables.sUTC_DST#">) as EncounterDate,
// end skip

$sql = "	Select 
		V.EncounterDate_UTC as EncounterDate,
		V.LMP,
		V.ExpectedDueDate,
		V.Temperature_Celsius, 
		V.Respiration_breathspermin,
		V.Waist_cm, 
		V.Weight_Kg, 
		V.Height_cm, 
		V.Pulse_beatspermin, 
		V.O2_Saturation_percent, 
	V.Systolic_mmHg, 
		V.Diastolic_mmHg, 
		V.Head_Circumference_cm, 
		V.VitalNotes,
		P.Sex
	From VitalsHistory V,
		PatientProfile P
	Where V.VitalsHistory_Id In(".$ComponentKey.")
		And V.Patient_Id=P.Patient_Id
		And (V.Hidden <> 1 OR V.Hidden IS NULL)
	Order BY V.EncounterDate_UTC DESC, V.VitalsHistory_Id DESC";


$VitalNotes = $this->ReportModel->data_db->query($sql);
$VitalNotes_num = $VitalNotes->num_rows();
$VitalNotes_row = $VitalNotes->row();

//<cfset Variables.VitalNote=0>
//
//<cfif VitalNotes.RecordCount NEQ 0>
//
//	<cfif caller.HeaderNeeded EQ True>
//		<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
//		<cfset caller.HeaderNeeded = False>
//		<cfset caller.NeedTemplateHeader = False>
//	</cfif>

$VitalNote = 0;
if ($VitalNotes_num != 0) {

//  if (HeaderNeeded) { //SKIPP
  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
//  }


//	<!---
//	The CFC call below returns the Font and Color information for the display of Chart Note body items.
//	Six items are returned:
//
//	Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//	Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//	Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//	Variables.BodyFontInfo.FontWeight = Bold or Normal
//	Variables.BodyFontInfo.FontStyle = Italics or Normal
//	Variables.BodyFontInfo.FontDecoration = Underline or None
//	--->
//	<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getBodyFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.HeaderKey)>
//	<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//	<cfset variables.ColumnHeaderStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: bold; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">

  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";

  $ColSep = 2;
?>  
  
	
	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
		<tr>
			<td width="7">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Date
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Time
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
      <?php
      if($VitalNotes_row->Sex == "F"){
        $Colspan = 20;
      ?>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
					LMP
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
					EDD
				</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
      <?php
      }else{
        $Colspan = 22;
      }
      ?>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Ht<br>
        <?php
        if($VitalsUnitsConfig_row->EnglishMetric == 0){
          echo "(in)";
        }else{
          echo "(cm)";
        }
        ?>
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Wt<br>
        <?php
        if($VitalsUnitsConfig_row->EnglishMetric == 0){
          echo "(lb)";
        }else{
          echo "(kg)";
        }
        ?>
			</td>	
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				BMI
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				BP
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Resp.
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Pulse
			</td>	
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Temp.<br>
        <?php
        if($VitalsUnitsConfig_row->EnglishMetric == 0){
          echo "(&deg;F)";
        }else{
          echo "(&deg;C)";
        }
        ?>
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				O2<br>
				Sat.
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Head<br>
				Cir.(cm)
			</td>
			<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
				Waist<br>
        <?php
        if($VitalsUnitsConfig_row->EnglishMetric == 0){
          echo "(in)";
        }else{
          echo "(cm)";
        }
        ?>
			</td>				
		</tr>

			<tr>
				<td width="7">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
           <?php echo date('m/d/Y', strtotime($VitalNotes_row->EncounterDate)); ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top" nowrap>
          <?php echo date('H:i s', strtotime($VitalNotes_row->EncounterDate)); ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
        <?php
        if($VitalNotes_row->Sex == "F"){
        ?>
					<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            <?php echo date('m/d/Y', strtotime($VitalNotes_row->LMP)); ?>
					</td>
					<td width="<?php echo $ColSep; ?>">&nbsp;</td>
					<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
             <?php echo date('m/d/Y', strtotime($VitalNotes_row->ExpectedDueDate)); ?>
					</td>
					<td width="<?php echo $ColSep; ?>">&nbsp;</td>
        <?php } ?>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#cmORinch(VitalNotes.Height_cm, VitalsUnitsConfig.EnglishMetric)#
           echo cmORinch($VitalNotes_row->Height_cm, $VitalsUnitsConfig_row->EnglishMetric);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#kgORlbs(VitalNotes.Weight_Kg, VitalsUnitsConfig.EnglishMetric)#
            echo kgORlbs($VitalNotes_row->Weight_Kg, $VitalsUnitsConfig_row->EnglishMetric);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#DisplayBMI(VitalNotes.Height_cm, VitalNotes.Weight_Kg)#
            echo DisplayBMI($VitalNotes_row->Height_cm, $VitalNotes_row->Weight_Kg);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#DisplayBP(VitalNotes.Systolic_mmHg, VitalNotes.Diastolic_mmHg)#
            echo DisplayBP($VitalNotes_row->Systolic_mmHg, $VitalNotes_row->Diastolic_mmHg);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#DisplayRespiration(VitalNotes.Respiration_Breathspermin)#
            echo DisplayRespiration($VitalNotes_row->Respiration_breathspermin);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
           <?php
          	#DisplayPulse(VitalNotes.Pulse_Beatspermin)#
           echo DisplayPulse($VitalNotes_row->Pulse_beatspermin);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#cecORfahr(VitalNotes.Temperature_Celsius, VitalsUnitsConfig.EnglishMetric)#
            echo cecORfahr($VitalNotes_row->Temperature_Celsius, $VitalsUnitsConfig_row->EnglishMetric);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#DisplayO2Sat(VitalNotes.O2_Saturation_percent)#
            echo DisplayO2Sat($VitalNotes_row->O2_Saturation_percent);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#DisplayHeadCirc(VitalNotes.Head_Circumference_cm)#
            echo DisplayHeadCirc($VitalNotes_row->Head_Circumference_cm);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
				<td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          	#cmORinch(VitalNotes.Waist_cm, VitalsUnitsConfig.EnglishMetric)#
            echo cmORinch($VitalNotes_row->Waist_cm, $VitalsUnitsConfig_row->EnglishMetric);
          ?>
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			</tr>
			
      <?php
      if($VitalNotes_row->VitalNotes != ""){
        $VitalNote = 1;
      }
      
      if($VitalNote == 1){
      ?>
			<tr>
				<td width="7">&nbsp;</td>	
				<td align="left" colspan="<?php echo $Colspan; ?>" style="width: 7.0in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
					<br>
					Vital Notes
				</td>
				<td width="<?php echo $ColSep; ?>">&nbsp;</td>
			</tr>

      <?php
      if(trim($VitalNotes_row->VitalNotes) != ""){
      ?>
					<tr>
						<td width="7">&nbsp;</td>	
						<td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
               <?php echo date('m/d/Y', strtotime($VitalNotes_row->EncounterDate)); ?>
						</td>
						<td width="<?php echo $ColSep; ?>">&nbsp;</td>
						<td align="left" colspan="<?php echo $Colspan; ?>" style="width: 6.5in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
              <?php echo trim($VitalNotes_row->VitalNotes); ?>
						</td>
					</tr>
    <?php
        }
      }
    ?>
	</table>
<?php
}
?>
<?php

$stop = TRUE;
if (!$stop):
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'Pregnancy History' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newPregnancyHistory.cfm
//													Case: 8899 - Created file
//													CASE 10,024
//	</note>
//	<io>
//		<in>
//		</in>
//
//		<out>
//		</out>
//	</io>
//
//  --->
//  
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.OrgId=Session.Org_Id>
//	<cfset Variables.sTimeOffset=Session.UTC_TimeOffset>
//	<cfset Variables.sDST=Session.UTC_DST>
//	<cfset Variables.sTimeZoneID=Session.UTC_TimeZoneId>
//</cflock>
//
//<cfif Trim(Attributes.UseDetailKeys) NEQ 1>
//	<cfset variables.bUseDetailKeys = false>
//<cfelse>
//	<cfset variables.bUseDetailKeys = true>
//</cfif>

  if ($UseDetailKeys != 1) {
    $bUseDetailKeys = FALSE;
  } else {
    $bUseDetailKeys = TRUE;
  }

//<cfset Variables.dataObj = StructNew()>
//<cfset Variables.dataObj.patientId = Attributes.PatientKey>
//<cfset Variables.dataObj.patient_Id = Attributes.PatientKey>
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sTimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sDST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sTimeZoneID>	
  $org = $this->OrgProfileModel->get_by_id($Encounter_dt->Org_ID)->row();
  $timezone = $this->TimeZoneModel->get_by_id($org->TimeZone_ID)->row();
  $dataObj = array(
      'patientId' => $Encounter_dt->Patient_ID,
      'patient_Id' => $Encounter_dt->Patient_ID,
      'orgTimeZoneDST' => $org->DST,
      'orgTimeZoneId' => $org->TimeZone_ID,
      'orgTimeZoneOffset' => $timezone->TzOffsetStandard_num,
      'SearchId' => NULL
  );



//<cfset Variables.TextStyle = ''>
//<!---CASE 10,024 --->
//<cfset Variables.ShowField = 0>
//
//<cfif variables.bUseDetailKeys eq true>
//	<cfset Variables.dataObj.RptType = Attributes.HEADERMASTERKEY>
//	<cfset Variables.dataObj.EncounterId = Attributes.PRIMARYKEY>
//	<cfset Variables.SearchIds = CreateObject("component","cfc.history.ChartNotes_History").getSearchIds(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//	<cfoutput query="Variables.SearchIds">
//		<cfset Variables.dataObj.SearchId = #Variables.SearchIds.ComponentKeys#>
//	</cfoutput>	
//</cfif>

  if ($bUseDetailKeys == TRUE) {
    $sql = "SELECT	E.ComponentKeys
			FROM	EncounterComponents E
			WHERE	E.Patient_ID = $PatientKey
					AND Encounter_ID = $PrimaryKey
					AND	HeaderMaster_ID = $HeaderMasterKey";
    $SearchIds = $this->ReportModel->data_db->query($sql);
    $SearchIds_num = $SearchIds->num_rows();
    $SearchIds_row = $SearchIds->row();

    $SearchId = $SearchIds_row->ComponentKeys;
    $dataObj['SearchId'] = $SearchId;
  }

//<cfif variables.bUseDetailKeys eq false>
//	<!--- Not Locked, We need the getHistoryFamilyRecords in History information query --->
//	<cfset Variables.PregnancyInfo = CreateObject("component","cfc.history.History").getHistoryPregnancyRecords(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<!--- Locked, We need the ChartNotesHistory information query --->
//	<cfset Variables.PregnancyInfo = CreateObject("component","cfc.history.ChartNotes_History").getHistoryPregnancyRecords(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>

  if ($bUseDetailKeys == FALSE) {

    $PregnancyInfo = getHistoryPregnancyRecords($user_db, $data_db, $dataObj);
  } else {
    $PregnancyInfo = getHistoryPregnancyRecords_ChartNotes($user_db, $data_db, $dataObj);
  }
//  SKIPP UTCtoLocalTZ 
//<cfif IsDefined('Attributes.RefHeader')>
//	<cfset Variables.TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;'>
//<cfelse>
//
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
//	<cfset Variables.TextStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//</cfif>
//
//<p />
//<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
//<cfset caller.HeaderNeeded = False>
//<cfset caller.NeedTemplateHeader = False>
//
//<cfif Variables.PregnancyInfo.DetailRecords.Recordcount eq 0>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput>
//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYLASTEDITEDBY) neq ''>
//				<tr>
//					<td width="7">&nbsp;</td>				
//					<td colspan="6" style="#Variables.TextStyle#" align="left" >&nbsp;<b>No Pregnancy History data</b></td>					
//				</tr>
//				<tr>
//					<td width="7">&nbsp;</td>				
//					<td width="1%" style="#Variables.TextStyle#" align="right" nowrap><strong>Last Edited On:</strong></td>
//					<td width="4">&nbsp;</td>				
//					<td style="#Variables.TextStyle#" >#Variables.PregnancyInfo.DetailRecords.DISPLAYLASTEDITEDBY#</td>
//					<td style="#Variables.TextStyle#" align="right" nowrap><strong>Last Edited By:</strong></td>
//					<td width="4">&nbsp;</td>				
//					<td style="#Variables.TextStyle#" >#DateFormat(Variables.PregnancyInfo.DetailRecords.LASTEDITDATE,'MM/DD/YYYY')#</td>							
//				</tr>	
//			<cfelse>
//				<tr>
//					<td width="7">&nbsp;</td>				
//					<td style="#Variables.TextStyle#" align="leftt" >&nbsp;<b>No Pregnancy History data</b></td>
//				</tr>	
//			</cfif>
//		</cfoutput>
//	</table>			
//</cfif>	
//

  if (isset($RefHeader)) {
    $TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;';
  } else {
    $data['HeaderKey'] = $HeaderKey;
    $data['PatientKey'] = $PatientKey;
    $data['HeaderMasterKey'] = $HeaderMasterKey;
    $data['FreeTextKey'] = $FreeTextKey;
    $data['SOHeaders'] = $SOHeaders;
    $this->load->view('encounter/print/componentheaders', $data);
  }




//
//<cfoutput query="Variables.PregnancyInfo.DetailRecords">
//

  if ($PregnancyInfo['DetailRecords_num'] != 0) {
    foreach ($PregnancyInfo['DetailRecords'] as $DetailRecord_dt) {


//  	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">	
      $html = '  	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">	';
//		<!--- CASE 10,024 Added missing header--->
//		<cfif IsDefined('Attributes.RefHeader')>
//			<!--- This is being called from printreferrals.cfm and needs a header --->
//			<tr>
//				<td colspan="12" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">#Attributes.RefHeader#</td>
//			</tr>	
//		</cfif>
      if (isset($RefHeader)) {
        $html .='
      <tr>
				<td colspan="12" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">' . $$RefHeader . '</td>
			</tr>	';
      }

//		<tr>
//			<td width="7">&nbsp;</td>				
//			<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" ><strong>Year:</strong></td>
//			<td width="4">&nbsp;</td>				
//			<td colspan="9" style="#Variables.TextStyle#" valign="top" >#Variables.PregnancyInfo.DetailRecords.YEAR#</td>
//		</tr>	
//		
      $html.='		<tr>
			<td width="7">&nbsp;</td>				
			<td width="1%" style="' . $TextStyle . '" align="right" valign="top" ><strong>Year:</strong></td>
			<td width="4">&nbsp;</td>				
			<td colspan="9" style="' . $TextStyle . '" valign="top" >' . $DetailRecord_dt->Year . '#</td>
		</tr>	';

//		<!--- CASE 10,024 Added cfif to display only if there is data --->
//		<cfif Trim(Variables.PregnancyInfo.DetailRecords.HOSPITAL) neq ''>	
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" ><strong>Hospital:</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td colspan="9" style="#Variables.TextStyle#" valign="top">#Variables.PregnancyInfo.DetailRecords.HOSPITAL#</td>
//			</tr>	
//		</cfif>	
//		
      if ($DetailRecord_dt->Hospital != '') {
        $html.='			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="' . $TextStyle . '" align="right" valign="top" ><strong>Hospital:</strong></td>
				<td width="4">&nbsp;</td>				
				<td colspan="9" style="' . $TextStyle . '" valign="top">' . $DetailRecord_dt->Hospital . '</td>
			</tr>	';
      }

//<!---			
//		ORIGINAL			
//		<tr>
//			<td width="15%" style="#Variables.TextStyle#" align="right"><strong>Weeks Gestation:</strong></td>
//			<td width="14%" style="#Variables.TextStyle#" >&nbsp;#Variables.PregnancyInfo.DetailRecords.WEEKSGESTATION#</td>
//			<td width="2%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
//			<td width="25%" style="#Variables.TextStyle#" align="right"><strong>Mother's total weight gain:</strong></td>
//				<cfset Variables.wglbs = 2.2046 * #Variables.PregnancyInfo.DetailRecords.WEIGHTGAIN_KG#>
//			<td colspan="4" style="#Variables.TextStyle#"  >&nbsp;#DecimalFormat(Variables.wglbs)#</td>							
//		</tr>	
//		
//--->			
//
//	<!--- CASE 10,024 Added cfif to display only if there is data --->
//		<cfif Variables.PregnancyInfo.DetailRecords.WEEKSGESTATION neq '' OR Variables.PregnancyInfo.DetailRecords.WEIGHTGAIN_KG neq 0>	

      if ($DetailRecord_dt->WeeksGestation != '' || $DetailRecord_dt->WeightGain_Kg != 0) {

//			<cfset Variables.H1 = ''>
//			<cfset Variables.D1 = ''>
//			<cfset Variables.H2 = ''>
//			<cfset Variables.D2 = ''>
//			<cfset Variables.H3 = ''>
//			<cfset Variables.D3 = ''>

        $H1 = '';
        $H2 = '';
        $H3 = '';
        $D1 = '';
        $D2 = '';
        $D3 = '';
//
//			<cfset Variables.ItemCounter = 0>
//
        $ItemCounter = 0;

//			<cfif trim(Variables.PregnancyInfo.DetailRecords.WEEKSGESTATION) neq ''>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Weeks Gestation:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.WEEKSGESTATION>
//			</cfif>
//
        if ($DetailRecord_dt->WeeksGestation != '') {
          $H1 = 'Weeks Gestation:';
          $D1 = $DetailRecord_dt->WeeksGestation;
        }
//
//			<cfif (Variables.PregnancyInfo.DetailRecords.WEIGHTGAIN_KG neq 0) and (trim(Variables.PregnancyInfo.DetailRecords.WEIGHTGAIN_KG) neq '')>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = "Mother's total weight gain:">
//				<cfset Variables.wglbs = 2.2046 * #Variables.PregnancyInfo.DetailRecords.WEIGHTGAIN_KG#>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.WEEKSGESTATION>
//			</cfif>
//			
        if ($DetailRecord_dt->WeightGain_Kg != 0 && $DetailRecord_dt->WeightGain_Kg != '') {
          $H2 = "Mother's total weight gain:";
          $wglbs = 2.2046 * $DetailRecord_dt->WeightGain_Kg;
          $D2 = $DetailRecord_dt->WeeksGestation;
        }
//
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H1#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top" nowrap>#Variables.D1#</td>
//				<td width="10">&nbsp;</td>
//				<td style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H2#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top" nowrap>#Variables.D2#</td>							
//				<td width="10">&nbsp;</td>				
//				<td width="15%" style="#Variables.TextStyle#" align="right" nowrap><strong>#Variables.H3#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top" >#Variables.D3#</td>							
//			</tr>

        $html.='			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H1 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top" nowrap>' . $D1 . '</td>
				<td width="10">&nbsp;</td>
				<td style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H2 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top" nowrap>' . $D2 . '</td>							
				<td width="10">&nbsp;</td>				
				<td width="15%" style="' . $TextStyle . '" align="right" nowrap><strong>' . $H3 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top" >' . $D3 . '</td>							
			</tr>';
//			
//		</cfif>			
//
      }
//<!---			Original 
//		<tr>
//			<td width="15%" style="#Variables.TextStyle#" align="right"><strong>Birth:</strong></td>
//			<td width="14%" style="#Variables.TextStyle#" >&nbsp;#Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY#</td>
//			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
//			<td width="25%" style="#Variables.TextStyle#" align="right"><strong>Number of hours in Labor:</strong></td>
//			<td width="9%" style="#Variables.TextStyle#"  >&nbsp;#DecimalFormat(Variables.PregnancyInfo.DetailRecords.HOURSLABOR)#</td>							
//			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>							
//			<td width="15%" style="#Variables.TextStyle#" align="right"><strong>Rhogam Injection:</strong></td>
//			<td width="14%" #Variables.TextStyle#  >&nbsp;#Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION#</td>							
//		</tr>	
//		
//--->			
//		
//		<!--- CASE 10,024 Added cfif to display only if there is data --->
//		<cfif  (Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY) neq '' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY) neq 'Not Asked' )
//			OR (Trim(Variables.PregnancyInfo.DetailRecords.HOURSLABOR) neq ''  AND Variables.PregnancyInfo.DetailRecords.HOURSLABOR neq 0)
//			OR ( Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION) neq 'Not Asked' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION) neq 'Not Asked')>
//

      if ((trim($DetailRecord_dt->DisplayDelivery) != '' && trim($DetailRecord_dt->DisplayDelivery) != 'Not Asked') || (trim($DetailRecord_dt->HoursLabor) != '' && $DetailRecord_dt->HoursLabor != 0) || (trim($DetailRecord_dt->DisplayRhogamInjection) != 'Not Asked' && $DetailRecord_dt->DisplayRhogamInjection != 'Not Asked')) {
//
//			<cfset Variables.H1 = ''>
//			<cfset Variables.D1 = ''>
//			<cfset Variables.H2 = ''>
//			<cfset Variables.D2 = ''>
//			<cfset Variables.H3 = ''>
//			<cfset Variables.D3 = ''>
//

        $H1 = '';
        $H2 = '';
        $H3 = '';
        $D1 = '';
        $D2 = '';
        $D3 = '';

//
//			<cfset Variables.ItemCounter = 0>
//
        $ItemCounter = 0;
//
//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY) neq '' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY) neq 'Not Asked' >
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Birth:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.DISPLAYDELIVERY>
//			</cfif>
//			
        if (trim($DetailRecord_dt->DisplayDelivery) != '' && trim($DetailRecord_dt->DisplayDelivery != 'Not Asked')) {
          $H1 = 'Birth:';
          $D1 = $DetailRecord_dt->DisplayDelivery;
        }
//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.HOURSLABOR) neq ''  AND Variables.PregnancyInfo.DetailRecords.HOURSLABOR neq 0>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Number of hours in Labor:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.HOURSLABOR>
//			</cfif>
//			
        if (trim($DetailRecord_dt->HoursLabor) != '' && $DetailRecord_dt->HoursLabor != 0) {
          $H2 = 'Number of hours in Labor:';
          $D2 = $DetailRecord_dt->HoursLabor;
        }

//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION) neq '' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION) neq 'Not Asked'>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Rhogam Injection:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.DISPLAYRHOGAMINJECTION>
//			</cfif>
//
        if (trim($DetailRecord_dt->DisplayRhogamInjection) != '' && trim($DetailRecord_dt->DisplayRhogamInjection) != 'Not Asked') {
          $H3 = 'Rhogam Injection:';
          $D3 = $DetailRecord_dt->DisplayRhogamInjection;
        }


//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" nowrap><strong>#Variables.H1#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top">#Variables.D1#</td>
//				<td width="10">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H2#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top">#Variables.D2#</td>							
//				<td width="10">&nbsp;</td>				
//				<td width="15%" style="#Variables.TextStyle#" align="right" nowrap><strong>#Variables.H3#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top" >#Variables.D3#</td>							
//			</tr>	
//		
        $html.='			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="' . $TextStyle . '" align="right" nowrap><strong>' . $H1 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top">' . $D1 . '</td>
				<td width="10">&nbsp;</td>				
				<td style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H2 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top">' . $D2 . '</td>							
				<td width="10">&nbsp;</td>				
				<td width="15%" style="' . $TextStyle . '" align="right" nowrap><strong>' . $H3 . '</strong></td>
				<td width="4">&nbsp;</td>				
				<td style="' . $TextStyle . '" valign="top" >' . $D3 . '</td>							
			</tr>	';
//		</cfif>	
      }
//
//<!---		Original	
//		<tr>
//			<td width="15%" style="#Variables.TextStyle#" align="right"><strong>Birth Weight:</strong></td>
//				<cfset Variables.bwlbs = 2.2046 * #Variables.PregnancyInfo.DetailRecords.WEIGHT_KG#>
//			<td width="14%" style="#Variables.TextStyle#" >&nbsp;#DecimalFormat(Variables.bwlbs)#</td>
//			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
//			<td width="25%" style="#Variables.TextStyle#" align="right"><strong>Birth Length:</strong></td>
//				<cfset Variables.blin = 2.54 * #Variables.PregnancyInfo.DetailRecords.LENGTH_CM#>
//			<td width="9%" style="#Variables.TextStyle#" >&nbsp;#DecimalFormat(Variables.blin)#</td>							
//			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>							
//			<td width="15%" style="#Variables.TextStyle#" align="right"><strong>Sex:</strong></td>
//			<td width="14%" style="#Variables.TextStyle#">&nbsp;#Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER#</td>							
//		</tr>	
//		
//--->		
//
//		<!--- CASE 10,024 Added cfif to display only if there is data --->
//		<cfif  (Trim(Variables.PregnancyInfo.DetailRecords.WEIGHT_KG) neq '' AND Variables.PregnancyInfo.DetailRecords.WEIGHT_KG neq 0 )
//			OR (Trim(Variables.PregnancyInfo.DetailRecords.LENGTH_CM) neq ''  AND Variables.PregnancyInfo.DetailRecords.LENGTH_CM neq 0)
//			OR ( Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER) neq '' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER) neq 'Not Asked')>
      if ((trim($DetailRecord_dt->Weight_Kg) != '' && $DetailRecord_dt->Weight_Kg != 0) || (trim($DetailRecord_dt->Length_cm) != '' && $DetailRecord_dt->Length_cm != 0) || (trim($DetailRecord_dt->DisplayGender) != '' && trim($DetailRecord_dt->DisplayGender) != 'Not Asked')
      ) {


//					
//			<cfset Variables.H1 = ''>
//			<cfset Variables.D1 = ''>
//			<cfset Variables.H2 = ''>
//			<cfset Variables.D2 = ''>
//			<cfset Variables.H3 = ''>
//			<cfset Variables.D3 = ''>
//			
        $H1 = '';
        $H2 = '';
        $H3 = '';
        $D1 = '';
        $D2 = '';
        $D3 = '';

//
//			<cfset Variables.ItemCounter = 0>
//			
        $ItemCounter = 0;
//
//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.WEIGHT_KG) neq '' AND Variables.PregnancyInfo.DetailRecords.WEIGHT_KG neq 0>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Birth Weight:'>
//				<cfset Variables.bwlbs = 2.2046 * #Variables.PregnancyInfo.DetailRecords.WEIGHT_KG#>
//				<cfset "Variables.D#Variables.ItemCounter#" = DecimalFormat(Variables.bwlbs)>
//			</cfif>
//			
        if (trim($DetailRecord_dt->Weight_Kg) != '' && $DetailRecord_dt->Weight_Kg != 0) {
          $H1 = 'Birth Weight:';
          $bwlbs = 2.2046 * $$DetailRecord_dt->Weight_Kg;
          $D1 = $bwlbs;
        }

//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.LENGTH_CM) neq ''  AND Variables.PregnancyInfo.DetailRecords.LENGTH_CM neq 0>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Birth Length:'>
//				<cfset Variables.blin = #Variables.PregnancyInfo.DetailRecords.LENGTH_CM# / 2.54>
//				<cfset "Variables.D#Variables.ItemCounter#" = DecimalFormat(Variables.blin)>
//			</cfif>

        if (trim($DetailRecord_dt->Length_cm) != '' && $DetailRecord_dt->Length_cm != 0) {
          $H2 = 'Birth Length:';
          $blin = $DetailRecord_dt->Length_cm / 2.54;
          $D2 = $blin;
        }

//			<cfif Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER) neq '' AND Trim(Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER) neq 'Not Asked'>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Sex:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.PregnancyInfo.DetailRecords.DISPLAYGENDER>
//			</cfif>
//			
        if (trim($DetailRecord_dt->DisplayGender) != '' && trim($DetailRecord_dt->DisplayGender) != 'Not Asked') {
          $H3 = 'Sex:';
          $D3 = $DetailRecord_dt->DisplayGender;
        }
//
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H1#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#"  valign="top" >#Variables.D1#</td>
//				<td width="10">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H2#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#"  valign="top" >#Variables.D2#</td>							
//				<td width="10">&nbsp;</td>				
//				<td width="15%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>#Variables.H3#</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="#Variables.TextStyle#" valign="top" >#Variables.D3#</td>							
//			</tr>
        $html.='//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H1 . '</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="' . $TextStyle . '"  valign="top" >' . $D1 . '</td>
//				<td width="10">&nbsp;</td>				
//				<td style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H2 . '</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="' . $TextStyle . '"  valign="top" >' . $D2 . '</td>							
//				<td width="10">&nbsp;</td>				
//				<td width="15%" style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>' . $H3 . '</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td style="' . $TextStyle . '" valign="top" >' . $D3 . '</td>							
//			</tr>';
//			
//		</cfif>				
      }
//		<!--- CASE 10,024 Added cfif to display only if there is data --->
//		<cfif Trim(Variables.PregnancyInfo.DetailRecords.NOTES) neq ''>
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top"><strong>Notes:</strong></td>
//				<td width="4">&nbsp;</td>				
//				<td colspan="9" style="#Variables.TextStyle#" valign="top" >#HTMLEditFormat(Trim(Variables.PregnancyInfo.DetailRecords.NOTES))#</td>
//			</tr>	
//		</cfif>					

      if (trim($DetailRecord_dt->Notes) != '') {
        $html.='			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="' . $TextStyle . '" align="right" valign="top"><strong>Notes:</strong></td>
				<td width="4">&nbsp;</td>				
				<td colspan="9" style="' . $TextStyle . '" valign="top" >' . $DetailRecord_dt->Notes . '</td>
			</tr>	';
      }
//
//
//		<!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
//		<cfloop query="Variables.PregnancyInfo.SmartControls">
//			<cfif Variables.PregnancyInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '1' AND Variables.PregnancyInfo.DetailRecords.HISTORYPREGNANCY_DTL_ID eq Variables.PregnancyInfo.SMARTCONTROLS.DETAILID>
//				<cfset Variables.ShowField = 1>
//			</cfif>
//		</cfloop>
//		
      $ShowField = 0;
      if ($PregnancyInfo['DetailRecords_num']) {
        foreach ($PregnancyInfo['SmartControls'] as $SmartControls_dt) {
          if ($SmartControls_dt->SmartControlAnswer == '1' && $SmartControls_dt->HistoryPregnancy_Dtl_ID == $SmartControls_dt->DetailID) {
            $ShowField = 1;
          }
        }

//		<cfif Variables.ShowField eq 1>
//			<cfset Variables.ShowField = 0>
//			
        if ($ShowField = 1) {
          $ShowField = 0;

//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top"><strong>Complications:</strong></td>
//				<td width="4">&nbsp;</td>			
//				
          $html.='			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="' . $TextStyle . '" align="right" valign="top"><strong>Complications:</strong></td>
				<td width="4">&nbsp;</td>			';
//				<td colspan="9" style="#Variables.TextStyle#" valign="top" >
//				
          $html.='<td colspan="9" style="' . $TextStyle . '" valign="top" >	';
//					<!---Create a list of all the answers for the Complications output to find the last one to change a ',' with an 'and' --->
//					<cfset Variables.Complist = ''>
//					<cfloop query="Variables.PregnancyInfo.SmartControls">
//						<cfif Variables.PregnancyInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '1' AND Variables.PregnancyInfo.DetailRecords.HISTORYPREGNANCY_DTL_ID eq Variables.PregnancyInfo.SMARTCONTROLS.DETAILID>
//							<cfif Variables.Complist eq ''>
//								<cfset Variables.Complist = Variables.PregnancyInfo.SmartControls.DISPLAYNAME>
//							<cfelse>				
//								<cfset Variables.Complist = Variables.Complist&','&Variables.PregnancyInfo.SmartControls.DISPLAYNAME>					
//							</cfif>
//						</cfif>	
//					</cfloop>
//					
          $Complist = '';
          foreach ($PregnancyInfo['SmartControls'] as $SmartControls_dt) {
            if ($SmartControls_dt->SmartControlAnswer == '1' && $SmartControls_dt->HistoryPregnancy_Dtl_ID == $SmartControls_dt->DetailID) {
              if ($Complist == '') {
                $Complist = $SmartControls_dt->DisplayName;
              } else {
                $Complist = $Complist . ',' . $SmartControls_dt->DisplayName;
              }
            }
          }
//
//					<cfset Variables.CompInfoCount = 0>
//					<cfset variables.ItemOutput = ""> 
//					
          $CompInfoCount = 0;
          $ItemOutput = 0;
//					<cfloop list="#Variables.Complist#" index="variables.I">
//					<cfif Variables.CompInfoCount eq 0>
//						<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							<cfset Variables.CompInfoCount = 1>
//						<cfelse>
//							<cfif variables.I eq ListLast(Variables.Complist)>
//								<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//							<cfelse>
//								<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//							</cfif>	
//							<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//						</cfif>
//					</cfloop>
//					
          $ItemOutput = $Complist;
//					<cfif Variables.CompInfoCount eq 0>
//						<cfset variables.ItemOutput = "&nbsp;">
//					</cfif>
//					#variables.ItemOutput#
//					
          if ($CompInfoCount == 0) {
            $ItemOutput = "&nbsp;";
          }
          $html.=$ItemOutput;
//				</td>
//			</tr>	
//			
          $html.='</td>
			</tr>		';
//		</cfif>		
        }
      }
//		<tr>
//			<td width="7">&nbsp;</td>				
//			<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>Last Edited On:</strong></td>
//			<td width="4">&nbsp;</td>				
//			<td colspan="2" style="#Variables.TextStyle#"  valign="top" nowrap>#DateFormat(Variables.PregnancyInfo.DetailRecords.LASTEDITDATE,'MM/DD/YYYY')#</td>
//			<td style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>Last Edited By:</strong></td>
//			<td width="4">&nbsp;</td>				
//			<td colspan="5"style="#Variables.TextStyle#"  valign="top" nowrap>#Variables.PregnancyInfo.DetailRecords.DISPLAYLASTEDITEDBY#</td>							
//		</tr>	
//		
      $html.='		<tr>
			<td width="7">&nbsp;</td>				
			<td width="1%" style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>Last Edited On:</strong></td>
			<td width="4">&nbsp;</td>				
			<td colspan="2" style="' . $TextStyle . '"  valign="top" nowrap>' . date('m/d/Y', strtotime($DetailRecord_dt->LastEditDate)) . ' </td>
			<td style="' . $TextStyle . '" align="right" valign="top" nowrap><strong>Last Edited By:</strong></td>
			<td width="4">&nbsp;</td>				
			<td colspan="5"style="' . $TextStyle . '"  valign="top" nowrap>' . $DetailRecord_dt->DisplayLastEditedBy . '</td>							
		</tr>	';
//		
//	</table>
//	<p />
//	
      $html.='
	</table>
	<p />';
//</cfoutput>
//
      echo $html;
    }
  }

endif;
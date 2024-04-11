<?php
//<!--- 10/18/2011 CH CASE 211 - Adding Spanish Language --->
//
//<!---CASE 211 - Set Default Language to English(1) then Check and see if we are only printing the Health Advice Report and if so, get the language of the patient, 	--->
//<cfparam name="url.PrintPHAOnly" default="0">
//<cfset Variables.PatientLang = 1>

$PrintPHAOnly = 0; // SKIPP
$PatientLang = 1;

//<cfif url.PrintPHAOnly eq 1>
//	<!--- Get the patient Language --->
//	<cfquery datasource="#Attributes.EMRDataSource#" name="getPatientLang">
//		SELECT 	Top 1
//				ISNULL(LanguageMaster_ID,1) AS PatientLang
//		FROM	PatientProfile
//		WHERE	Patient_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#Attributes.PatientKey#">
//	</cfquery>


if ($PrintPHAOnly) {

  $sqlPatientLang = "SELECT 	Top 1
				ISNULL(LanguageMaster_ID,1) AS PatientLang
		FROM	" . $data_db . ".dbo.PatientProfile
		WHERE	Patient_id = $PatientKey";

  $PatientLang = $this->ReportModel->data_db->query($sqlPatientLang);
  $PatientLang_row = $PatientLang->row();

//	<!--- Check to make sure the patient Language is a valid Language in our Database--->
//	<cfquery datasource="#Attributes.EMRDataSource#" name="VerifyPatientLang">
//		SELECT	Top 1
//				LanguageMaster_ID
//		FROM	LanguageMaster
//		WHERE	LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#getPatientLang.PatientLang#">
//				AND (hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR hidden is null)
//	</cfquery>

  $sqlVerifyPatientLang = "		SELECT	Top 1
				LanguageMaster_ID
		FROM	" . $data_db . ".dbo.LanguageMaster
		WHERE	LanguageMaster_ID = $PatientLang_row->PatientLang
				AND (hidden = 0 OR hidden is null)";


  $VerifyPatientLang = $this->ReportModel->data_db->query($sqlVerifyPatientLang);
  $VerifyPatientLang_num = $VerifyPatientLang->num_rows();
  $VerifyPatientLang_row = $VerifyPatientLang->row();
//
//	<!---If it matches set Other (4) or French (3) equal to English (1), If it's not valid, set it to English (1) --->
//	<cfif VerifyPatientLang.Recordcount neq 1>
//		<cfset Variables.PatientLang = 1>
//	<cfelse>
//		<cfif getPatientLang.PatientLang eq 4>
//			<cfset Variables.PatientLang = 1>
//		<cfelseif getPatientLang.PatientLang eq 3>
//			<cfset Variables.PatientLang = 1>
//		<cfelse>
//			<cfset Variables.PatientLang = getPatientLang.PatientLang>
//		</cfif>
//	</cfif>
//</cfif>

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
//
//<!---CASE 211 - Get data for the language of the patient --->
//<cfquery datasource="#Attributes.EMRDataSource#" name="getAWACSAdvice">
//	select
//		al_L.DisplayName as category,
//		am_L.recommendation,
//		am_L.accomplishby,
//		al_L.sortorder as CategorySortOrder,
//		am_L.sortorder as AdviceSortOrder,
//		<!---CASE 211 - am_L.LanguageMaster_ID --->
//		am_L.LanguageMaster_ID
//	From ecastmaster.dbo.awacsadvicemaster As am
//		<!---CASE 211 - Added Join to L_awacsadvicemaster --->
//		Join ecastmaster.dbo.L_awacsadvicemaster As am_L
//			On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
//		Inner Join ecastmaster.dbo.awacsadvicelist As al
//			On am.awacsadvicelist_id = al.awacsadvicelist_id
//		<!---CASE 211 - Added Join to L_awacsadvicelist --->
//		Join ecastmaster.dbo.L_awacsadvicelist As al_L
//			On al.awacsadvicelist_id = al_L.awacsadvicelist_id
//		Left join ecastmaster.dbo.awacsadvicemap As map
//			On am.awacsadvicemaster_ID = map.awacsadvicemaster_ID
//		Left join awacsinput As ai
//			on (ai.awacsriskmaster_ID = map.awacsriskmaster_ID and ai.awacsriskmaster_ID is not null)
//				or (ai.tbotmaster_ID = map.tbotmaster_ID and ai.tbotmaster_ID is not null)
//	Where
//		<!---CASE 211 - Added ( --->
//		(((ai.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//			and (ai.datavalue = am.severity)
//			and (ai.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">))
//		Or ((am.missing_tbotmaster_ID is not null)
//			and (not exists (select tbotmaster_ID
//							from awacsinput
//							where (encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//								and (tbotmaster_ID = am.missing_tbotmaster_ID)
//								and (hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">) )))
//		)
//		<!---CASE 211 - Added 2 ANDs 	--->
//		AND am_L.LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#Variables.PatientLang#">
//		AND al_L.LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#Variables.PatientLang#">
//	Union
//
//	Select
//		al_L.DisplayName as category,
//		am_L.recommendation,
//		am_L.accomplishby,
//		al_L.sortorder as CategorySortOrder,
//		am_L.sortorder as AdviceSortOrder,
//		<!---CASE 211 - am_L.LanguageMaster_ID --->
//		am_L.LanguageMaster_ID
//	From awacsresults As ar
//	Join ecastmaster.dbo.awacsadvicemap As map
//		On map.awacsseveritymaster_ID = ar.awacsseveritymaster_ID
//	join ecastmaster.dbo.awacsadvicemaster As am
//		On map.awacsadvicemaster_ID = am.awacsadvicemaster_ID
//	<!---CASE 211 - Added Join to L_awacsadvicemaster --->
//	Join ecastmaster.dbo.L_awacsadvicemaster As am_L
//		On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
//	Inner Join ecastmaster.dbo.awacsadvicelist As al
//		On am.awacsadvicelist_id = al.awacsadvicelist_id
//	<!---CASE 211 - Added Join to L_awacsadvicelist --->
//	Join ecastmaster.dbo.L_awacsadvicelist As al_L
//		On al.awacsadvicelist_id = al_L.awacsadvicelist_id
//	Where (ar.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//		And ar.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//		And am.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//		<!---CASE 211 - Added 2 ANDs		 --->
//		AND am_L.LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#Variables.PatientLang#">
//		AND al_L.LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#Variables.PatientLang#">
//
//	Order by CategorySortOrder, AdviceSortOrder
//<!---
//	select
//		al.DisplayName as category,
//		am.recommendation,
//		am.accomplishby,
//		al.sortorder as CategorySortOrder,
//		am.sortorder as AdviceSortOrder
//	From ecastmaster.dbo.awacsadvicemaster As am
//	Inner Join ecastmaster.dbo.awacsadvicelist As al
//		On am.awacsadvicelist_id = al.awacsadvicelist_id
//	Left join ecastmaster.dbo.awacsadvicemap As map
//		On am.awacsadvicemaster_ID = map.awacsadvicemaster_ID
//	Left join awacsinput As ai
//		on (ai.awacsriskmaster_ID = map.awacsriskmaster_ID and ai.awacsriskmaster_ID is not null)
//			or (ai.tbotmaster_ID = map.tbotmaster_ID and ai.tbotmaster_ID is not null)
//	Where ((ai.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//			and (ai.datavalue = am.severity)
//			and (ai.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">))
//		Or ((am.missing_tbotmaster_ID is not null)
//			and (not exists (select tbotmaster_ID
//							from awacsinput
//							where (encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//								and (tbotmaster_ID = am.missing_tbotmaster_ID)
//								and (hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">) )))
//
//	Union
//
//	Select
//		al.DisplayName as category,
//		am.recommendation,
//		am.accomplishby,
//		al.sortorder as CategorySortOrder,
//		am.sortorder as AdviceSortOrder
//	From awacsresults As ar
//	Join ecastmaster.dbo.awacsadvicemap As map
//		On map.awacsseveritymaster_ID = ar.awacsseveritymaster_ID
//	join ecastmaster.dbo.awacsadvicemaster As am
//		On map.awacsadvicemaster_ID = am.awacsadvicemaster_ID
//	Inner Join ecastmaster.dbo.awacsadvicelist As al
//		On am.awacsadvicelist_id = al.awacsadvicelist_id
//	Where (ar.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.ComponentKey#">)
//		And ar.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//		And am.hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//
//	Order by CategorySortOrder, AdviceSortOrder
//
//
//
//--->
//
//</cfquery>

$ComponenKeyVar = $ComponentKey;

//$sqlgetAWACSAdvice = "	select
//		al_L.DisplayName as category,
//		am_L.recommendation,
//		am_L.accomplishby,
//		al_L.sortorder as CategorySortOrder,
//		am_L.sortorder as AdviceSortOrder,
//		am_L.LanguageMaster_ID
//	From ecastmaster.dbo.awacsadvicemaster As am
//		Join ecastmaster.dbo.L_awacsadvicemaster As am_L
//			On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
//		Inner Join ecastmaster.dbo.awacsadvicelist As al
//			On am.awacsadvicelist_id = al.awacsadvicelist_id
//		Join ecastmaster.dbo.L_awacsadvicelist As al_L
//			On al.awacsadvicelist_id = al_L.awacsadvicelist_id
//		Left join ecastmaster.dbo.awacsadvicemap As map
//			On am.awacsadvicemaster_ID = map.awacsadvicemaster_ID
//		Left join awacsinput As ai
//			on (ai.awacsriskmaster_ID = map.awacsriskmaster_ID and ai.awacsriskmaster_ID is not null)
//				or (ai.tbotmaster_ID = map.tbotmaster_ID and ai.tbotmaster_ID is not null)
//	Where
//		(((ai.encounter_ID = $ComponenKeyVar)
//			and (ai.datavalue = am.severity)
//			and (ai.hidden = 0))
//		Or ((am.missing_tbotmaster_ID is not null)
//			and (not exists (select tbotmaster_ID
//							from awacsinput
//							where (encounter_ID = $ComponenKeyVar)
//								and (tbotmaster_ID = am.missing_tbotmaster_ID)
//								and (hidden = 0) )))
//		)
//		AND am_L.LanguageMaster_ID = $PatientLang
//		AND al_L.LanguageMaster_ID = $PatientLang
//	Union
//	Select
//		al_L.DisplayName as category,
//		am_L.recommendation,
//		am_L.accomplishby,
//		al_L.sortorder as CategorySortOrder,
//		am_L.sortorder as AdviceSortOrder,
//		am_L.LanguageMaster_ID
//	From awacsresults As ar
//	Join ecastmaster.dbo.awacsadvicemap As map
//		On map.awacsseveritymaster_ID = ar.awacsseveritymaster_ID
//	join ecastmaster.dbo.awacsadvicemaster As am
//		On map.awacsadvicemaster_ID = am.awacsadvicemaster_ID
//	Join ecastmaster.dbo.L_awacsadvicemaster As am_L
//		On am.awacsadviceMaster_id = am_L.awacsadviceMaster_id
//	Inner Join ecastmaster.dbo.awacsadvicelist As al
//		On am.awacsadvicelist_id = al.awacsadvicelist_id
//	Join ecastmaster.dbo.L_awacsadvicelist As al_L
//		On al.awacsadvicelist_id = al_L.awacsadvicelist_id
//	Where (ar.encounter_ID = $ComponenKeyVar)
//		And ar.hidden = 0
//		And am.hidden = 0
//		AND am_L.LanguageMaster_ID = $PatientLang
//		AND al_L.LanguageMaster_ID = $PatientLang
//	Order by CategorySortOrder, AdviceSortOrder	";
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

//QUERY VERY SLOWLy
$AWACSAdvice = $this->ReportModel->data_db->query($sqlgetAWACSAdvice);
$AWACSAdvice_num = $AWACSAdvice->num_rows();
$AWACSAdvice_result = $AWACSAdvice->result();


//var_dump($AWACSAdvice_result);
//<cfif getAWACSAdvice.RecordCount NEQ 0>
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
//	<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//	<cfset variables.ColumnHeaderStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: bold; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">

if ($AWACSAdvice_num != 0) {

//  if (HeaderNeeded) { //SKIPP
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
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
//	<!---CASE 211 - Get the headers in the language of the patient --->
//	<cfquery datasource="#Attributes.EMRDataSource#" name="getAWACSAdviceHeaders">
//		SELECT	AWACSAdviceHeader_ID,
//				SortOrder,
//				HeaderText
//		FROM	ecastmaster.dbo.L_AWACSAdviceHeader
//		WHERE	LanguageMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#Variables.PatientLang#">
//	</cfquery>

  $sql = "SELECT	AWACSAdviceHeader_ID,
  				SortOrder,
  				HeaderText
  		FROM	ecastmaster.dbo.L_AWACSAdviceHeader
  		WHERE	LanguageMaster_ID = $PatientLang";

  $getAWACSAdviceHeaders = $this->ReportModel->data_db->query($sql);
  $getAWACSAdviceHeaders_num = $getAWACSAdviceHeaders->num_rows();
  $getAWACSAdviceHeaders_result = $getAWACSAdviceHeaders->result();

//	<cfset Variables.ColHead1 = ''>
//	<cfset Variables.ColHead2 = ''>
//	<cfset Variables.ColHead3 = ''>

  $ColHead1 = "";
  $ColHead2 = "";
  $ColHead3 = "";

//	<cfloop query="getAWACSAdviceHeaders">
//		<cfset "Variables.ColHead#getAWACSAdviceHeaders.AWACSAdviceHeader_ID#" = getAWACSAdviceHeaders.HeaderText>
//	</cfloop>


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
      <!-- remove first td #new dev
      <td width="7">&nbsp;</td> -->
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
      <!-- remove first td #new dev
      <td width="7">&nbsp;</td>-->
      <td>

      <!--				<cfset variables.RowBorderBegin = "border-top:solid 3px; border-bottom:solid 3px; border-right:solid 3px; border-left:solid 3px; padding:2px;">
                      <cfset variables.RowBorder = "border-top:solid 3px; border-bottom:solid 3px; border-right:solid 3px; padding:2px;">-->

        <?php
        $RowBorderBegin = "  border:solid 3px; border-left:solid 3px; border-right:solid 3px; border-bottom:solid 3px; padding:2px;";
        $RowBorder = " ; border-top:solid 3px; border-bottom:solid 3px; border-right:solid 3px; padding:2px;";
        $RowBorderEnd = "  border-right:solid 3px;   border-bottom:solid 3px; padding:2px;";
        $RowBorderBull = "border-bottom:solid 3px;";
        ?>
        <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
          <tr>
            <td nowrap align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <!--#ucase(Variables.ColHead1)# -Category--->
              <?php
              echo strtoupper($ColHead1);
              ?>
            </td>
            <td  colspan="2" align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <!--#ucase(Variables.ColHead2)# -RECOMMENDATION--->
              <?php
              echo strtoupper($ColHead2);
              ?>
            </td>
            <td nowrap align="left"  valign="top" style="<?php echo "font-weight: bold !important; " .$DefaultStyle.' '. $RowBorderBegin; ?>">
              <!--#ucase(Variables.ColHead3)# -ACCOMPLISH BY--->
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

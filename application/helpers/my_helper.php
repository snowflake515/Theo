<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

if (!function_exists('form_value')) {

  function form_value($var = '', $data = null, $isarray = FALSE) {
    $dt_ci = & get_instance();
    $dt_post = $dt_ci->input->post();
    if (strpos($var, '[') !== false) {
      $exp = explode('[', $var);
      $str = explode(']', $exp[1]);
      $temp = $dt_post[$exp[0]][$str[0]];
      $result = ($temp && $temp != "") ? $temp : (($data != null) ? $data->$str[0] : null);
    } else {
      $result = ($dt_ci->input->post($var) != "") ? $dt_ci->input->post($var) : (($data != null) ? $data->$var : null);
    }
    return $result;
  }
}

if (!function_exists('option_select')) {

  function option_select($options = array(), $id = NULL, $value = NULL, $blank = "[Select]", $show_blank = FALSE) {
    $result = array();
    if (!$show_blank) {
      $result[''] = $blank;
    }
    foreach ($options as $val) {
      $result[$val->$id] = $val->$value;
    }
    if ($id == 'Relationship_ID') {
      $result[(intval($val->$id) + 1)] = "Primary Care Provider";
    }
    return $result;
  }

}

if (!function_exists('datetime_format')) {

  function datetime_format($val) {
    return ($val != NULL) ? date('Y-m-d H:i', strtotime($val)) : NULL;
  }

}

if (!function_exists('time_format')) {

  function time_format($val) {
    if ($val != NULL) {
      return strtolower(date('h:i A', strtotime($val)));
    } else {
      return NULL;
    }
  }

}

if (!function_exists('dob_to_age')) {

  function dob_to_age($dob) {
    $dob = date("Y-m-d", strtotime($dob));

    $dobObject = new DateTime($dob);
    $nowObject = new DateTime();

    $diff = $dobObject->diff($nowObject);

    return $diff->y;
  }

}

if (!function_exists('date_format_only')) {

  function date_format_only($val) {
    $ress = NULL;
    if ($val != NULL) {
      $check = substr($val, 0, 4);
      if (strpos($check, '-')) {
        $ress = $val;
      } else {
        $ress = date('m-d-Y', strtotime($val));
      }
    }
    return $ress;
    // return ($val != NULL) ? date('m-d-Y', strtotime($val)) : NULL;
  }

}


if (!function_exists('disabled')) {

  function disabled($id) {
    return ($id == NULL) ? "disabled='disabled'" : NULL;
  }

}

if (!function_exists('disabled_ecnounter')) {

  function disabled_ecnounter($id) {
    return ($id == 1) ? "disabled='disabled'" : NULL;
  }

}


if (!function_exists('time_picker')) {

  function time_picker() {
    return ($id == 1) ? "disabled='disabled'" : NULL;
  }

}


if (!function_exists('long_field_odbc')) {

  function long_field_odbc($str = "") {
    $active = TRUE;
    $limit = 31;
    if ($active) {
      $str = substr($str, 0, $limit);
    }
    return $str;
  }

}


if (!function_exists('convert_date_form')) {

  function convert_date_form($val = "", $field, $obj) {
    if ($val != "") {
      if (!isset($_POST[$field]) || !strpos($_POST[$field], '/')) {
        return date('m-d-Y', strtotime($obj->$field));
      } else {
        return $_POST[$field];
      }
    }
  }

}

if (!function_exists('getBodyFontInfo')) {

  function getBodyFontInfo($data, $HeaderKey) {

    $dt_ci = & get_instance();


    $sql = "Select TOP 1
				H.BodyStyle,
				H.BodySize,
				H.BodyColor,
				F.FontName
			From " . $data['data_db'] . ".dbo.EncounterHeaders H
			Join " . $data['data_db'] . ".dbo.Fonts F
				On H.BodyFont_Id = F.Font_Id
			Where H.Header_Id=$HeaderKey";



    $BodySettings = $dt_ci->ReportModel->data_db->query($sql);
    $BodySettings_num = $BodySettings->num_rows();
    $BodySettings_row = $BodySettings->row();

    $body['FontColor'] = "black";
    $body['FontSize'] = "12";
    $body['FontFace'] = "Times New Roman";
    $body['FontWeight'] = "normal";
    $body['FontStyle'] = "normal";
    $body['FontDecoration'] = "none";

    if ($BodySettings_num != 0) {

      $body['FontColor'] = $BodySettings_row->BodyColor;
      $body['FontSize'] = $BodySettings_row->BodySize;
      $body['FontFace'] = $BodySettings_row->FontName;

      if ($BodySettings_row->BodyStyle == 'B') {
        $body['FontWeight'] = "bold";
      } else if ($BodySettings_row->BodyStyle == 'I') {
        $body['FontStyle'] = "italic";
      } else if ($BodySettings_row->BodyStyle == 'U') {
        $body['FontDecoration'] = "underline";
      }
    }

    return $body;
  }

}


if (!function_exists('getChartHeaderFontInfo')) {

  function getChartHeaderFontInfo($data, $ConfigKey) {

    $dt_ci = & get_instance();

    $sql = "Select TOP 1
				EC.HeaderStyle as FontStyle,
				EC.HeaderSize as FontSize,
				EC.HeaderColor as FontColor,
				F.FontName
			From " . $data['data_db'] . ".dbo.EncounterConfig EC
			Join " . $data['data_db'] . ".dbo.Fonts F
				On EC.HeaderFont_Id = F.Font_Id
			Where EC.EncounterConfig_Id=$ConfigKey";



    $FontSettings = $dt_ci->ReportModel->data_db->query($sql);
    $FontSettings_num = $FontSettings->num_rows();
    $FontSettings_row = $FontSettings->row();

    $body['FontColor'] = "black";
    $body['FontSize'] = "12";
    $body['FontFace'] = "Times New Roman";
    $body['FontWeight'] = "normal";
    $body['FontStyle'] = "normal";
    $body['FontDecoration'] = "none";

    if ($FontSettings_num != 0) {

      $body['FontColor'] = $FontSettings_row->FontColor;
      $body['FontSize'] = $FontSettings_row->FontSize;
      $body['FontFace'] = $FontSettings_row->FontName;

      if ($FontSettings_row->FontStyle == 'B') {
        $body['FontWeight'] = "bold";
      } else if ($FontSettings_row->FontStyle == 'I') {
        $body['FontStyle'] = "italic";
      } else if ($FontSettings_row->FontStyle == 'U') {
        $body['FontDecoration'] = "underline";
      }
    }

    return $body;
  }

}


if (!function_exists('getChartFooterFontInfo')) {

  function getChartFooterFontInfo($data, $ConfigKey = 0) {

    $dt_ci = & get_instance();

    $sql = "Select TOP 1
				EC.FooterStyle as FontStyle,
				EC.FooterSize as FontSize,
				EC.FooterColor as FontColor,
				F.FontName
			From " . $data['data_db'] . ".dbo.EncounterConfig EC
			Join " . $data['data_db'] . ".dbo.Fonts F
				On EC.HeaderFont_Id = F.Font_Id
			Where EC.EncounterConfig_Id=$ConfigKey";

    $FontSettings = $dt_ci->ReportModel->data_db->query($sql);
    $FontSettings_num = $FontSettings->num_rows();
    $FontSettings_row = $FontSettings->row();

    $body['FontColor'] = "black";
    $body['FontSize'] = "12";
    $body['FontFace'] = "Times New Roman";
    $body['FontWeight'] = "normal";
    $body['FontStyle'] = "normal";
    $body['FontDecoration'] = "none";

    if ($FontSettings_num != 0) {

      $body['FontColor'] = $FontSettings_row->FontColor;
      $body['FontSize'] = $FontSettings_row->FontSize;
      $body['FontFace'] = $FontSettings_row->FontName;

      if ($FontSettings_row->BodyStyle == 'B') {
        $body['FontWeight'] = "bold";
      } else if ($FontSettings_row->BodyStyle == 'I') {
        $body['FontStyle'] = "italic";
      } else if ($FontSettings_row->BodyStyle == 'U') {
        $body['FontDecoration'] = "underline";
      }
    }

    return $body;
  }

}

if (!function_exists('getHistoryPregnancyRecords')) {

//  <cffunction name="getHistoryPregnancyRecords" output="false" access="public" returntype="Struct">
//		<cfargument name="Domain" type="String" required="true" hint="Domain for Database Identification."/>
//		<cfargument name="dataObj" type="Struct" required="true" hint="data.">
//
//		<cfset var Local = StructNew() />
//		<cfset Local.Result = StructNew() />
//		<cfset Local.MemberDetailIds = ""/>
//
//		<cfquery name="Local.SelectHistoryDetailRecords" datasource="#getEMRDataDSN(Arguments.Domain)#">
//			SELECT H.HistoryPregnancy_ID,
//				H.Org_ID, H.Patient_ID,
//				H.HistoryPregnancy_Dtl_ID,
//				H.DatePopulated_UTC,
//                D.HistoryPregnancy_Dtl_ID,
//				D.HistoryPregnancy_ID,
//				D.Org_ID,
//				D.Hospital,
//				D.Year,
//				D.WeightGain_Kg,
//				D.WeeksGestation,
//				D.HoursLabor,
//				D.Delivery_HistoryDropdownMaster_ID,
//				D.RhogamInjection_HistoryDropdownMaster_ID,
//				D.Gender_HistoryDropdownMaster_ID,
//				D.Length_cm,
//				D.Weight_Kg,
//				D.Notes,
//				D.LastEditedBy_Users_PK,
//				D.LastEditedOn_UTC,
//				D.Hidden,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Delivery_HistoryDropdownMaster_ID) as DisplayDelivery,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.RhogamInjection_HistoryDropdownMaster_ID) as DisplayRhogamInjection,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Gender_HistoryDropdownMaster_ID) as DisplayGender,
//				(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname
//					   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials
//				  END
//				 From #getDSNPrefix(Arguments.Domain)#eCast_Data.dbo.Users U
//				 Where U.ID=D.LastEditedBy_Users_PK) as DisplayLastEditedBy,
//				 dbo.UTCtoLocalTZ(D.LastEditedOn_UTC,<cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#Arguments.dataObj.orgTimeZoneOffset#">,<cfqueryparam cfsqltype="CF_SQL_BIT" value="#Arguments.dataObj.orgTimeZoneDST#">,<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) as LastEditDate,
//				(SELECT TOP 1 CASE WHEN <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Arguments.dataObj.orgTimeZoneDST#"> = 1 THEN CASE WHEN dbo.IsDSTActiveTZ(D.LastEditedOn_UTC,<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) = 1 THEN tz.tzAbbrDaylight ELSE tz.tzAbbrStandard END ELSE tz.tzAbbrStandard END FROM TimeZone tz where tz.timezone_id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) as LastEditDateTZAbbr
//			FROM      HistoryPregnancy H, HistoryPregnancy_Dtl D
//			WHERE     (H.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#Arguments.dataObj.PatientID#">) AND (H.HistoryPregnancy_Dtl_ID = D.HistoryPregnancy_Dtl_ID)
//					AND ((D.Hidden <> 1) OR (D.Hidden IS NULL))
//		</cfquery>
//
//		<cfset Local.Result.DetailRecords = Local.SelectHistoryDetailRecords/>
//
//
//		<cfloop query="Local.SelectHistoryDetailRecords">
//			<cfset Local.MemberDetailIds = Local.MemberDetailIds & HistoryPregnancy_Dtl_ID & ",">
//		</cfloop>
//
//		<cfquery name="Local.SelectSmartControls" datasource="#getEMRDataDSN(Arguments.Domain)#">
//
//
//
//				SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName,
//				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy,
//				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID,
//				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
//				FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
//					    	(SELECT HistoryPregnancy_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryPregnancy_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment
//					    	FROM HistoryPregnancy_Answer
//					    	WHERE  	(HistoryPregnancy_Dtl_ID in (<cfqueryparam list="true" separator="," value="#Local.MemberDetailIds#">))) AS A
//					    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
//				WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL) AND (SC.Type = 'F')
//
//
//
//
//		</cfquery>
//
//		<cfset Local.Result.SmartControls = Local.SelectSmartControls/>
//
//		<cfreturn Local.Result />
//	</cffunction>

  function getHistoryPregnancyRecords($user_db = "", $data_db = "", $dataObj = array()) {
    $dt_ci = & get_instance();
    $Local = array();
    $Result = array();
    $MemberDetailIds = "";
    $dataObj = (object) $dataObj;
    $sql = "			SELECT H.HistoryPregnancy_ID,
				H.Org_ID, H.Patient_ID,
				H.HistoryPregnancy_Dtl_ID,
				H.DatePopulated_UTC,
        D.HistoryPregnancy_Dtl_ID,
				D.HistoryPregnancy_ID,
				D.Org_ID,
				D.Hospital,
				D.Year,
				D.WeightGain_Kg,
				D.WeeksGestation,
				D.HoursLabor,
				D.Delivery_HistoryDropdownMaster_ID,
				D.RhogamInjection_HistoryDropdownMaster_ID,
				D.Gender_HistoryDropdownMaster_ID,
				D.Length_cm,
				D.Weight_Kg,
				D.Notes,
				D.LastEditedBy_Users_PK,
				D.LastEditedOn_UTC,
				D.Hidden,
				(SELECT TOP 1 DisplayName FROM " . $data_db . ".dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Delivery_HistoryDropdownMaster_ID) as DisplayDelivery,
				(SELECT TOP 1 DisplayName FROM " . $data_db . ".dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.RhogamInjection_HistoryDropdownMaster_ID) as DisplayRhogamInjection,
				(SELECT TOP 1 DisplayName FROM " . $data_db . ".dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Gender_HistoryDropdownMaster_ID) as DisplayGender,
				(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname
			   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials
				 END
				 From " . $user_db . ".dbo.Users U
				 Where U.ID=D.LastEditedBy_Users_PK) as DisplayLastEditedBy,
				 " . $data_db . ".dbo.UTCtoLocalTZ(D.LastEditedOn_UTC,$dataObj->orgTimeZoneOffset,$dataObj->orgTimeZoneDST,$dataObj->orgTimeZoneId) as LastEditDate,
				 (SELECT TOP 1 CASE WHEN $dataObj->orgTimeZoneDST = 1 THEN CASE WHEN " . $data_db . ".dbo.IsDSTActiveTZ(D.LastEditedOn_UTC,$dataObj->orgTimeZoneId) = 1 THEN tz.tzAbbrDaylight ELSE tz.tzAbbrStandard END ELSE tz.tzAbbrStandard END
         FROM " . $data_db . ".dbo.TimeZone tz where tz.timezone_id = $dataObj->orgTimeZoneId) as LastEditDateTZAbbr
			   FROM      " . $data_db . ".dbo.HistoryPregnancy H, " . $data_db . ".dbo.HistoryPregnancy_Dtl D
			   WHERE     (H.Patient_ID = $dataObj->patientId) AND (H.HistoryPregnancy_Dtl_ID = D.HistoryPregnancy_Dtl_ID)
					AND ((D.Hidden <> 1) OR (D.Hidden IS NULL))";


    $GetAdmendmentID = $dt_ci->ReportModel->data_db->query($sql);
    $Result['DetailRecords'] = $GetAdmendmentID->result();
    $Result['DetailRecords_num'] = $GetAdmendmentID->num_rows();
    $Result['SmartControls'] = NULL;
    $MemberDetailIds = array();

    foreach ($GetAdmendmentID->result() as $GetAdmendmentID_dt) {
      $MemberDetailIds[] = $$GetAdmendmentID_dt->HistoryPregnancy_Dtl_ID;
    }
    $MemberDetailIds = implode(',', $MemberDetailIds);

    if ($MemberDetailIds) {
      $sql = "	SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName,
				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy,
				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID,
				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
				FROM	 " . $data_db . ".dbo.HistorySmartControlsMaster SC LEFT OUTER JOIN
					    	(SELECT HistoryPregnancy_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryPregnancy_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment
					    	FROM  " . $data_db . ".dbo.HistoryPregnancy_Answer
					    	WHERE  	(HistoryPregnancy_Dtl_ID in ($MemberDetailIds))) AS A
					    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
				WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL) AND (SC.Type = 'F')

				";
      $SelectSmartControls = $dt_ci->ReportModel->data_db->query($sql);
      $Result['SmartControls'] = $SelectSmartControls->result();
    }

    return $Result;
  }

}





if (!function_exists('getHistoryPregnancyRecords_ChartNotes')) {


//
//  <cffunction name="getHistoryPregnancyRecords" output="false" access="public" returntype="Struct">
//		<cfargument name="Domain" type="String" required="true" hint="Domain for Database Identification."/>
//		<cfargument name="dataObj" type="Struct" required="true" hint="data.">
//
//		<cfset var Local = StructNew() />
//		<cfset Local.Result = StructNew() />
//		<cfset Local.MemberDetailIds = ""/>
//
//		<cfquery name="Local.SelectHistoryDetailRecords" datasource="#getEMRDataDSN(Arguments.Domain)#">
//			SELECT
//                D.HistoryPregnancy_Dtl_ID,
//				D.HistoryPregnancy_ID,
//				D.Org_ID,
//				D.Hospital,
//				D.Year,
//				D.WeightGain_Kg,
//				D.WeeksGestation,
//				D.HoursLabor,
//				D.Delivery_HistoryDropdownMaster_ID,
//				D.RhogamInjection_HistoryDropdownMaster_ID,
//				D.Gender_HistoryDropdownMaster_ID,
//				D.Length_cm,
//				D.Weight_Kg,
//				D.Notes,
//				D.LastEditedBy_Users_PK,
//				D.LastEditedOn_UTC,
//				D.Hidden,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Delivery_HistoryDropdownMaster_ID) as DisplayDelivery,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.RhogamInjection_HistoryDropdownMaster_ID) as DisplayRhogamInjection,
//				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Gender_HistoryDropdownMaster_ID) as DisplayGender,
//				(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname
//					   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials
//				  END
//				 From #getDSNPrefix(Arguments.Domain)#eCast_Data.dbo.Users U
//				 Where U.ID=D.LastEditedBy_Users_PK) as DisplayLastEditedBy,
//				 dbo.UTCtoLocalTZ(D.LastEditedOn_UTC,<cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#Arguments.dataObj.orgTimeZoneOffset#">,<cfqueryparam cfsqltype="CF_SQL_BIT" value="#Arguments.dataObj.orgTimeZoneDST#">,<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) as LastEditDate,
//				(SELECT TOP 1 CASE WHEN <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Arguments.dataObj.orgTimeZoneDST#"> = 1 THEN CASE WHEN dbo.IsDSTActiveTZ(D.LastEditedOn_UTC,<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) = 1 THEN tz.tzAbbrDaylight ELSE tz.tzAbbrStandard END ELSE tz.tzAbbrStandard END FROM TimeZone tz where tz.timezone_id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) as LastEditDateTZAbbr
//			FROM      HistoryPregnancy_Dtl D
//			WHERE     D.HistoryPregnancy_Dtl_ID IN (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" separator=","  value="#Arguments.dataObj.SearchId#">)
//					AND ((D.Hidden <> 1) OR (D.Hidden IS NULL))
//		</cfquery>
//
//		<cfset Local.Result.DetailRecords = Local.SelectHistoryDetailRecords/>
//
//
//		<cfloop query="Local.SelectHistoryDetailRecords">
//			<cfset Local.MemberDetailIds = Local.MemberDetailIds & HistoryPregnancy_Dtl_ID & ",">
//		</cfloop>
//
//		<cfquery name="Local.SelectSmartControls" datasource="#getEMRDataDSN(Arguments.Domain)#">
//				SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName,
//				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy,
//				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID,
//				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
//				FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
//					    	(SELECT HistoryPregnancy_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryPregnancy_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment
//					    	FROM HistoryPregnancy_Answer
//					    	WHERE  	(HistoryPregnancy_Dtl_ID in (<cfqueryparam list="true" separator="," value="#Local.MemberDetailIds#">))) AS A
//					    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
//				WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL) AND (SC.Type = 'F')
//		</cfquery>
//
//		<cfset Local.Result.SmartControls = Local.SelectSmartControls/>
//
//		<cfreturn Local.Result />
//	</cffunction>

  function getHistoryPregnancyRecords_ChartNotes($user_db = "", $data_db = "", $dataObj = array()) {
    $dt_ci = & get_instance();
    $Local = array();
    $Result = array();
    $MemberDetailIds = "";
    $dataObj = (object) $dataObj;
    if ($dataObj->SearchId) {
      $sql = "SELECT
        D.HistoryPregnancy_Dtl_ID,
				D.HistoryPregnancy_ID,
				D.Org_ID,
				D.Hospital,
				D.Year,
				D.WeightGain_Kg,
				D.WeeksGestation,
				D.HoursLabor,
				D.Delivery_HistoryDropdownMaster_ID,
				D.RhogamInjection_HistoryDropdownMaster_ID,
				D.Gender_HistoryDropdownMaster_ID,
				D.Length_cm,
				D.Weight_Kg,
				D.Notes,
				D.LastEditedBy_Users_PK,
				D.LastEditedOn_UTC,
				D.Hidden,
				(SELECT TOP 1 DisplayName FROM $data_db.dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Delivery_HistoryDropdownMaster_ID) as DisplayDelivery,
				(SELECT TOP 1 DisplayName FROM $data_db.dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.RhogamInjection_HistoryDropdownMaster_ID) as DisplayRhogamInjection,
				(SELECT TOP 1 DisplayName FROM $data_db.dbo.HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Gender_HistoryDropdownMaster_ID) as DisplayGender,
				(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname
					   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials
				  END
				 From $user_db.dbo.Users U
				 Where U.ID=D.LastEditedBy_Users_PK) as DisplayLastEditedBy,
				 $data_db.dbo.UTCtoLocalTZ(D.LastEditedOn_UTC,$dataObj->orgTimeZoneOffset,$dataObj->orgTimeZoneDST,$dataObj->orgTimeZoneId) as LastEditDate,
				(SELECT TOP 1 CASE WHEN $dataObj->orgTimeZoneDST = 1 THEN CASE WHEN $data_db.dbo.IsDSTActiveTZ(D.LastEditedOn_UTC,$dataObj->orgTimeZoneId) = 1 THEN tz.tzAbbrDaylight ELSE tz.tzAbbrStandard END ELSE tz.tzAbbrStandard END FROM $data_db.dbo.TimeZone tz where tz.timezone_id = $dataObj->orgTimeZoneId) as LastEditDateTZAbbr
			FROM      $data_db.dbo.HistoryPregnancy_Dtl D
			WHERE     D.HistoryPregnancy_Dtl_ID IN ($dataObj->SearchId)
					AND ((D.Hidden <> 1) OR (D.Hidden IS NULL))";

      $SelectHistoryDetailRecords = $dt_ci->ReportModel->data_db->query($sql);
      $Result['DetailRecords'] = $SelectHistoryDetailRecords->result();
      $Result['DetailRecords_num'] = $SelectHistoryDetailRecords->num_rows();
      $Result['SmartControls'] = NULL;
      $MemberDetailIds = array();

      foreach ($SelectHistoryDetailRecords->result() as $SelectHistoryDetailRecords_dt) {
        $MemberDetailIds[] = $SelectHistoryDetailRecords_dt->HistoryPregnancy_Dtl_ID;
      }
      $MemberDetailIds = implode(',', $MemberDetailIds);

      if ($MemberDetailIds) {
        $sql = "				SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName,
				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy,
				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID,
				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
				FROM	$data_db.dbo.HistorySmartControlsMaster SC LEFT OUTER JOIN
					    	(SELECT HistoryPregnancy_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryPregnancy_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment
					    	FROM $data_db.dbo.HistoryPregnancy_Answer
					    	WHERE  	(HistoryPregnancy_Dtl_ID in ($MemberDetailIds))) AS A
					    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
				WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL) AND (SC.Type = 'F')";
        $SelectSmartControls = $dt_ci->ReportModel->data_db->query($sql);
        $Result['SmartControls'] = $SelectSmartControls->result();
      }
    }

    return $Result;
  }

}

if (!function_exists('my_show_404')) {

  function my_show_404() {
    $CI = & get_instance();
    $data['partial'] = "errors/404";
    $CI->load->view('layout', $data);
    echo $CI->output->get_output();
    exit;
  }

}




if (!function_exists('show_my_post')) {

  function show_my_post($unset = array()) {
    $CI = & get_instance();

    $str = '$post = $this->get_params();<br/><br/>';
    $str .= "private function get_params(){<br/>";
    $str .= '$post = array(<br/>';
    foreach ($CI->input->post() as $key => $value) {
      if (in_array($key, $unset) == FALSE) {
        $str .="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'$key' => " . '$this->input->post' . "('" . $key . "'),<br/>";
      }
    }
    $str .= ');<br/>';
    $str .='return $post;<br/>';
    $str .="}";
    echo $str;
    exit();
  }

}


if (!function_exists('show_my_rules')) {

  function show_my_rules($unset = array()) {
    $CI = & get_instance();

    $str = '$post = $this->rules_validation();<br/><br/>';
    $str .= "private function rules_validation(){<br/>";
    $str .= '$rules = array(<br/>';
    foreach ($CI->input->post() as $key => $value) {
      if (in_array($key, $unset) == FALSE) {
        $str .="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array(
                'field' => '$key',
                'label' => '$key',
                'rules' => ''),<br/>";
      }
    }
    $str .= ');<br/>';
    $str .='return $rules;<br/>';
    $str .="}";
    echo $str;
    exit();
  }

}


if (!function_exists('default_config_pagging')) {

  function default_config_pagging() {
    $config['full_tag_open'] = '<ul class="pagination pagination_patients"> ';
    $config['full_tag_close'] = '</ul>';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';
    $config['use_page_numbers'] = TRUE;
    $config['cur_tag_open'] = ' <li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    return $config;
  }

}


if (!function_exists('show_my_validate')) {
  function show_my_validate($unset = array()) {
    $CI = & get_instance();
    $str = '  array(<br/>';
    foreach ($CI->input->post() as $key => $value) {
      if (in_array($key, $unset) == FALSE) {
        $str .="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; array('field'=> '$key', 'label' => '$key',  'rules' => 'trim'),<br/>";
      }
    }
    $str .= ');<br/>';

    echo $str;
    exit();
  }
}


if (!function_exists('show_my_post')) {
  function show_my_post($unset = array()) {
    $CI = & get_instance();
    $str = '  array(<br/>';
    foreach ($CI->input->post() as $key => $value) {
      if (in_array($key, $unset) == FALSE) {
        $str .="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'$key' => " . '$this->input->post' . "('" . $key . "'),<br/>";
      }
    }
    $str .= ');<br/>';
    echo $str;
    exit();
  }
}

<?php

if (!function_exists('cmORinch')) {

  function cmORinch($Value_cm, $EngMetric, $DisplayUnits = 0) {
    if (trim($Value_cm) == "") {
      $RetValue = "";
    } else if ($EngMetric == 0) {
      $RetValue = round(($Value_cm * 0.3937008) * 100) / 100;
    } else {
      $RetValue = round($Value_cm * 100) / 100;
    }
    if ($DisplayUnits == 1 && $RetValue != "") {
      if ($EngMetric == 0) {
        $RetValue = $RetValue . " in";
      } else {
        $RetValue = $RetValue . " cm";
      }
    }
    return $RetValue;
  }

}

if (!function_exists('kgORlbs')) {

  function kgORlbs($Value_Kg, $EngMetric, $DisplayUnits = 0) {
    if (trim($Value_Kg) == "") {
      $RetValue = "";
    } else {
      if ($EngMetric == 0) {
        $RetValue = round(($Value_Kg * 2.2) * 100) / 100;
      } else {
        $RetValue = round(Value_Kg * 100) / 100;
      }
    }

    if ($DisplayUnits == 1 && $RetValue != "") {
      if ($EngMetric == 0) {
        $WeightDisplay = (int) $RetValue / 1;
        $WeightOunces = round(($RetValue - $WeightDisplay) * 16);
        $RetValue = $RetValue . " " . $WeightOunces . " oz";
      } else {
        $RetValue = $RetValue . " Kg";
      }
    }
    return $RetValue;
  }

}


if (!function_exists('DisplayBMI')) {

  function DisplayBMI($Height_cm, $Weight_Kg, $ReturnForZero = "") {
    if ($Height_cm != 0 && $Height_cm != "" && $Weight_Kg != 0 && $Weight_Kg != "") {
      $RetValue = Round(($Weight_Kg / (($Height_cm / 100) * ($Height_cm / 100))) * 100) / 100;
    } else {
      $RetValue = "";
    }

    return $RetValue;
  }

}

if (!function_exists('DisplayBP')) {

  function DisplayBP($Systolic, $Diastolic) {
    if ($Systolic != 0 && $Systolic != "" && $Diastolic != 0 && $Diastolic != "") {
      $RetValue = ($Systolic / 1) / ($Diastolic / 1);
    } else {
      $RetValue = "";
    }
    return $RetValue;
  }

}

if (!function_exists('DisplayRespiration')) {

  function DisplayRespiration($Respiration, $DisplayUnits = 0) {
    if ($Respiration != "") {
      $RetValue = ($Respiration / 1);
    } else {
      $RetValue = "";
    }

    if ($DisplayUnits == 1 && $RetValue != "") {
      $RetValue = $RetValue . " breaths/min";
    }
    return $RetValue;
  }

}

if (!function_exists('DisplayPulse')) {

  function DisplayPulse($Pulse, $DisplayUnits = 0) {
    if ($Pulse != "") {
      $RetValue = ($Pulse / 1);
    } else {
      $RetValue = "";
    }
    if ($DisplayUnits == 1 && $RetValue != "") {
      $RetValue = $RetValue . " beats/min";
    }
    return $RetValue;
  }

}

if (!function_exists('cecORfahr')) {

  function cecORfahr($Value_C, $EngMetric, $DisplayUnits = 0) {
    if (trim($Value_C) == "") {
      $RetValue = "";
    } else if ($EngMetric == 0) {
      $RetValue = round((($Value_C * (9 / 5)) + 32) * 10) / 10;
    } else {
      $RetValue = round($Value_C * 10) / 10;
    }

    if ($DisplayUnits == 1 && $RetValue != "") {
      if ($EngMetric == 0) {
        $RetValue = $RetValue . " &deg;F";
      } else {
        $RetValue = $RetValue . " deg;C";
      }
    }
    return $RetValue;
  }

}

if (!function_exists('DisplayO2Sat')) {

  function DisplayO2Sat($O2Saturation, $DisplayUnits = 0) {
    if ($O2Saturation != "") {
      $RetValue = ($O2Saturation / 1);
    } else {
      $RetValue = "";
    }

    if ($DisplayUnits == 1 && $RetValue != "") {
      $RetValue = $RetValue . " &";
    }
    return $RetValue;
  }

}

if (!function_exists('DisplayHeadCirc')) {

  function DisplayHeadCirc($HeadCircumference, $DisplayUnits = 0) {
    if ($HeadCircumference != "") {
      $RetValue = ($RetValue / 1);
    } else {
      $RetValue = "";
    }

    if ($DisplayUnits == 1 && $RetValue != "") {
      $RetValue = $RetValue . ' cm';
    }
    return $RetValue;
  }

}

if (!function_exists('encountercomponents')) {

  function encountercomponents($dt) {

//<cfquery datasource="#Attributes.EMRDataSource#" name="Component">
//Select EncounterComponents_Id,
//       EncounterText,
//       ComponentKeys,
//	   UseDetailKeys
//  From EncounterComponents
// Where HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderMasterKey#">
//   And Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PatientKey#">
//   <cfif Attributes.FreeTextKey EQ 1>
//       And Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//   </cfif>
//</cfquery>
//
//
    $sql_add = NULL;
    if ($dt['FreeTextKey'] == 1) {
      $sql_add = "And Encounter_Id=" . $dt['PrimaryKey'];
    }
    $sql = "
Select EncounterComponents_Id,
       EncounterText,
       ComponentKeys,
	   UseDetailKeys
  From " . $dt['data_db'] . ".dbo.EncounterComponents
 Where HeaderMaster_Id=" . $dt['HeaderMasterKey'] . "
   And Patient_Id=" . $dt['PatientKey'] . "
   $sql_add";

//echo $sql;
    $dt_ci = & get_instance();
    $Component = $dt_ci->ReportModel->data_db->query($sql);
    $Component_num = $Component->num_rows();
    $Component_result = $Component->result();
    $Component_row = $Component->row();

    $return_data = array();
//
//<cfif Component.UseDetailKeys NEQ "">
//	<cfset Caller.UseDetailKeys=Component.UseDetailKeys>
//<cfelse>
//	<cfset Caller.UseDetailKeys=0>
//</cfif>
//


    if ($Component_num && $Component_row->UseDetailKeys != "") {
      $return_data['UseDetailKeys'] = $Component_row->UseDetailKeys;
    } else {
      $return_data['UseDetailKeys'] = 0;
    }

//
//<cfif Component.EncounterComponents_Id NEQ "">
//	<cfset Caller.EncounterComponentPrimaryKey=Component.EncounterComponents_Id>
//<cfelse>
//	<cfset Caller.EncounterComponentPrimaryKey=0>
//</cfif>
//
//
    if ($Component_num && $Component_row->EncounterComponents_Id != "") {
      $return_data['EncounterComponentPrimaryKey'] = $Component_row->EncounterComponents_Id;
    } else {
      $return_data['EncounterComponentPrimaryKey'] = 0;
    }

//
//<cfif Component.ComponentKeys NEQ "">
//	<cfset Caller.ComponentKey=Component.ComponentKeys>
//<cfelse>
//	<cfset Caller.ComponentKey=0>
//</cfif>
//
//

    if ($Component_num && $Component_row->ComponentKeys != "") {
      $return_data['ComponentKey'] = $Component_row->ComponentKeys;
    } else {
      $return_data['ComponentKey'] = 0;
    }



//
//<cfif Component.EncounterText NEQ "">
//	<cfset Caller.EncounterComponentKey=Component.EncounterComponents_Id>
//<cfelse>
//	<cfset Caller.EncounterComponentKey=0>
//</cfif>
//
//

    if ($Component_num && $Component_row->EncounterText != "") {
      $return_data['EncounterComponentKey'] = $Component_row->EncounterComponents_Id;
    } else {
      $return_data['EncounterComponentKey'] = 0;
    }


//
//
//<cfif Component.RecordCount EQ 0>
//	<cfset Caller.EncounterComponentKey=0>
//	<cfset Caller.ComponentKey=0>
//</cfif>


    if ($Component_num == 0) {
      $return_data['EncounterComponentKey'] = 0;
      $return_data['ComponentKey'] = 0;
    }



    return $return_data;
  }

}
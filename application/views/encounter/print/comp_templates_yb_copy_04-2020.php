<?php

//
//<cfquery datasource="#Attributes.EMRDataSource#" name="ETLProcess">
//Select TOP 1
//       ETLSaved
//  From ETL
// Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//</cfquery>
//

$sql = "Select TOP 1
       ETLSaved
  From ETL
 Where Encounter_ID = $PrimaryKey";
$ETLProcess = $this->ReportModel->data_db->query($sql);
$ETLProcess_num = $ETLProcess->num_rows();
$ETLProcess_row = $ETLProcess->row();


//
//<cfif ETLProcess.RecordCount NEQ 0>
//
if ($ETLProcess_num != 0) {
//	<cfif ETLProcess.ETLSaved EQ 1>


  if ($ETLProcess_row->ETLSaved == 1) {
//		<cfquery datasource="#Attributes.EMRDataSource#" name="ETL2IDS">
//			Select DISTINCT
//			       TML2_Id
//			  From ETL2
//			 Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//		</cfquery>


    $sql = "Select DISTINCT
			       TML2_ID
			  From ETL2
			   Where Encounter_ID=$PrimaryKey";
    $ETL2IDS = $this->ReportModel->data_db->query($sql);
    $ETL2IDS_num = $ETL2IDS->num_rows();
    $ETL2IDS_row = $ETL2IDS->row();
    $ETL2IDS_result = $ETL2IDS->result();



//
//        <cfquery datasource="#Attributes.EMRDataSource#" name="ETL3IDS">
//		Select DISTINCT
//		       TML3_Id
//		  From ETL3
//		 Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//		</cfquery>
//

    $sql = "Select DISTINCT
		       TML3_ID
		  From ETL3Input
		 Where Encounter_ID=$PrimaryKey";
    $ETL3IDS = $this->ReportModel->data_db->query($sql);
    $ETL3IDS_num = $ETL3IDS->num_rows();
    $ETL3IDS_row = $ETL3IDS->row();
    $ETL3IDS_result = $ETL3IDS->result();


//
//		<cfquery datasource="#Attributes.EMRDataSource#" name="GetETL3Input">
//		Select DISTINCT
//		       TML3_Id,
//		       ETL3Input
//		  From ETL3Input
//		 Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//		</cfquery>
//
//    $sql = "	Select DISTINCT
//		       TML3_ID,
//		       ETL3Input
//		  From ETL3Input
//		 Where Encounter_ID=$PrimaryKey";


    $sql = "Select
      MAX(ETL3Input_Id) as ETL3Input_ID,
      TML3_ID
      From ETL3Input
      where Encounter_Id = $PrimaryKey
      group by TML3_Id";

    $GetETL3Input = $this->ReportModel->data_db->query($sql);
    $GetETL3Input_num = $GetETL3Input->num_rows();
    $GetETL3Input_row = $GetETL3Input->row();
    $GetETL3Input_result = $GetETL3Input->result();




//
//		<cfset L3InputStruct=StructNew()>
//		<cfif GetETL3Input.RecordCount NEQ 0>
//			<cfloop query="GetETL3Input">
//				<cfset Temp=StructInsert(L3InputStruct,GetETL3Input.TML3_Id,"#Trim(GetETL3Input.ETL3Input)#",TRUE)>
//			</cfloop>
//		</cfif>
//
    /* ==================


     *
     */

    /*
     * tablet input loop

      $sql = "SELECT * FROM TabletInput
      WHERE
      Encounter_ID = $PrimaryKey
      AND (COALESCE(Status, '') <> 'X')
      ORDER BY TabletInput_ID ASC";
      $GetETL3Input = $this->ReportModel->data_db->query($sql);
      $GetETL3Input_num = $GetETL3Input->num_rows();
      $GetETL3Input_row = $GetETL3Input->row();
      $GetETL3Input_result = $GetETL3Input->result();

      $ETL2IDS_arr = array();
      $L3InputStruct = array();
      $ETL3IDS_arr = array();


      foreach ($GetETL3Input_result as $GetETL3Input_dt) {
      //$e_input = $this->ETL3InputModel->get_by_id($GetETL3Input_dt->ETL3Input_ID)->row();

      $L3InputStruct[$GetETL3Input_dt->TML3_ID] = $GetETL3Input_dt->TML3_Value;
      $ETL3IDS_arr[] = $GetETL3Input_dt->TML3_ID;
      $ETL2IDS_arr[] = $GetETL3Input_dt->TML2_ID;
      }
     */

    foreach ($GetETL3Input_result as $GetETL3Input_dt) {
      $e_input = $this->ETL3InputModel->get_by_id($GetETL3Input_dt->ETL3Input_ID)->row();
      $L3InputStruct[$GetETL3Input_dt->TML3_ID] = $e_input->ETL3Input;
    }






//
//		<cfif ETL2IDS.RecordCount NEQ 0 AND ETL3IDS.RecordCount NEQ 0>
//
    if ($ETL2IDS_num != 0 && $ETL3IDS_num != 0) {
      // if (count($ETL3IDS_arr) != 0 && count($ETL2IDS_arr) != 0) {

      $ETL2IDS_arr = array();
      foreach ($ETL2IDS_result as $ETL2IDS_dt) {
        $ETL2IDS_arr[] = $ETL2IDS_dt->TML2_ID;
      }
      $ETL3IDS_arr = array();
      foreach ($ETL3IDS_result as $ETL3IDS_dt) {
        $ETL3IDS_arr[] = $ETL3IDS_dt->TML3_ID;
      }
//			<!--- If the template is for History of Present Illnes (26), Problems (40), or Plan (97) we need to process it differently. --->
//			<cfif (Attributes.HeaderMasterKey EQ 26) OR (Attributes.HeaderMasterKey EQ 40) OR (Attributes.HeaderMasterKey EQ 97)>
      if ($HeaderMasterKey == 26 || $HeaderMasterKey == 40 || $HeaderMasterKey == 97) {
//				<!--- In order to know if I need to change the HPI Header as I go, find the distinct TML1_IDs--->
//		        <cfquery datasource="#Attributes.TemplateDataSource#" name="CountTML1IDs">
//					Select Distinct
//						T1.TML1_Id,
//						T1.Sequence,
//						T1.TML1_Description
//					From #Attributes.TemplateDataSource#.dbo.TML2 T2
//					JOIN #Attributes.TemplateDataSource#.dbo.TML3 T3
//						ON T2.TML2_ID=T3.TML2_Id
//					JOIN #Attributes.TemplateDataSource#.dbo.TML1 T1
//						ON T2.TML1_ID=T1.TML1_Id
//					Where T2.TML2_HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.HeaderMasterKey#">
//						And T2.TML2_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL2IDS.TML2_Id,',')#">)
//						And T3.TML3_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL3IDS.TML3_Id,',')#">)
//						And (T2.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T2.Hidden IS NULL)
//						And (T3.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T3.Hidden IS NULL)
//						And (T1.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T1.Hidden IS NULL)
//					Order By T1.Sequence
//				</cfquery>

        $sql = "Select Distinct
						T1.TML1_ID,
						T1.Sequence,
						T1.TML1_Description
					From $template_db.dbo.TML2 T2
					JOIN $template_db.dbo.TML3 T3
						ON T2.TML2_ID=T3.TML2_ID
					JOIN $template_db.dbo.TML1 T1
						ON T2.TML1_ID=T1.TML1_ID
					Where T2.TML2_HeaderMaster_ID=$HeaderMasterKey
						And T2.TML2_ID IN (" . implode(',', $ETL2IDS_arr) . ")
						And T3.TML3_ID IN (" . implode(',', $ETL3IDS_arr) . ")
						And (T2.Hidden<> 1 OR T2.Hidden IS NULL)
						And (T3.Hidden<> 1 OR T3.Hidden IS NULL)
						And (T1.Hidden<> 1 OR T1.Hidden IS NULL)
					Order By T1.Sequence";
        $CountTML1IDs = $this->ReportModel->data_db->query($sql);
        $CountTML1IDs_num = $CountTML1IDs->num_rows();
        $CountTML1IDs_row = $CountTML1IDs->row();
        $CountTML1IDs_result = $CountTML1IDs->result();
//
//				<cfloop query="CountTML1IDs">
        foreach ($CountTML1IDs_result as $CountTML1IDs_dt) {


//			        <cfquery datasource="#Attributes.TemplateDataSource#" name="TML">
//						Select T2.TML2_Id,
//							T2.TML2_Sentence,
//							T3.TML3_TextToType,
//							T3.TML3_Id
//						From #Attributes.TemplateDataSource#.dbo.TML2 T2
//						JOIN #Attributes.TemplateDataSource#.dbo.TML3 T3
//							ON T2.TML2_ID=T3.TML2_Id
//						JOIN #Attributes.TemplateDataSource#.dbo.TML1 T1
//							ON T2.TML1_ID=T1.TML1_Id
//						Where T2.TML2_HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.HeaderMasterKey#">
//							And T2.TML2_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL2IDS.TML2_Id,',')#">)
//							And T3.TML3_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL3IDS.TML3_Id,',')#">)
//							And (T2.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T2.Hidden IS NULL)
//							And (T3.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T3.Hidden IS NULL)
//							And (T1.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T1.Hidden IS NULL)
//							And (T1.TML1_ID=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#CountTML1IDs.TML1_ID#">)
//						Order By T1.Sequence, T2.Sequence, T3.Sequence
//					</cfquery>
          $abnormal = (isset($summary_report) ? 'And T3.Abnormal = 1  ' : "");
          $sql = "Select T2.TML2_ID,
							T2.TML2_Sentence,
							T3.TML3_TextToType,
							T3.TML3_ID,
              T3.TheoQuestion_ID,
              T3.TheoAnswer_ID
						From $template_db.dbo.TML2 T2
						JOIN $template_db.dbo.TML3 T3
							ON T2.TML2_ID=T3.TML2_ID
						JOIN $template_db.dbo.TML1 T1
							ON T2.TML1_ID=T1.TML1_ID
						Where T2.TML2_HeaderMaster_ID= $HeaderMasterKey
							And T2.TML2_ID IN (" . implode(',', $ETL2IDS_arr) . ")
							And T3.TML3_ID IN (" . implode(',', $ETL3IDS_arr) . ")
							And (T2.Hidden<> 1 OR T2.Hidden IS NULL)
							And (T3.Hidden<> 1 OR T3.Hidden IS NULL)
							And (T1.Hidden<> 1 OR T1.Hidden IS NULL)
							And (T1.TML1_ID= $CountTML1IDs_dt->TML1_ID)
              $abnormal
						Order By T1.Sequence, T2.Sequence, T3.Sequence";
          $TML = $this->ReportModel->data_db->query($sql);
          $TML_num = $TML->num_rows();
          $TML_row = $TML->row();
          $TML_result = $TML->result();
//
//					<cfset TML2Struct=StructNew()>
//					<cfloop query="TML">
//						<cfset Temp=StructInsert(TML2Struct,TML.TML2_Sentence,TML.TML3_Id,TRUE)>
//					</cfloop>
          $TML2Struct = array();
          foreach ($TML_result as $TML_dt) {
            $TML2Struct["$TML_dt->TML2_Sentence"] = $TML_dt->TML3_ID;
          }
//					<cfif TML.RecordCount NEQ 0>
          if ($TML_num != 0) {
//						<cfif Attributes.OutPutMasterKey NEQ Attributes.HeaderMasterKey>
            if ($OutPutMasterKey != $HeaderMasterKey) {
//							<cfif caller.NeedTemplateHeader EQ True>

              if ($NeedTemplateHeader == TRUE) {
//								<cfquery datasource="#Attributes.EMRDataSource#" name="HeaderSettingsTemplate" maxrows="1">
//								Select TOP 1
//								       H.Header_Id,
//								       H.HeaderMaster_Id,
//								       H.HeaderText,
//									   H.HeaderStyle,
//									   H.HeaderSize,
//									   H.HeaderColor,
//									   F.FontName
//								  From EncounterHeaders H
//								  Join Fonts F
//								    On H.Font_Id=F.Font_Id
//								 Where H.HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderMasterKey#">
//								   And H.Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ProviderKey#">
//		   						   And H.EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.EncounterDescriptionKey#">
//								   And (H.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR H.Hidden IS NULL)
//								</cfquery>

                $sql = "	Select TOP 1
								       H.Header_ID,
								       H.HeaderMaster_ID,
								       H.HeaderText,
									   H.HeaderStyle,
									   H.HeaderSize,
									   H.HeaderColor,
									   F.FontName
								  From EncounterHeaders H
								  Join Fonts F
								    On H.Font_ID=F.Font_ID
								 Where H.HeaderMaster_ID=$HeaderMasterKey
								   And H.Provider_ID=$ProviderKey
		   						   And H.EncounterDescription_ID=$EncounterDescriptionKey
								   And (H.Hidden<>1 OR H.Hidden IS NULL)";
                $HeaderSettingsTemplate = $this->ReportModel->data_db->query($sql);
                $HeaderSettingsTemplate_num = $HeaderSettingsTemplate->num_rows();
                $HeaderSettingsTemplate_row = $HeaderSettingsTemplate->row();
                $HeaderSettingsTemplate_result = $HeaderSettingsTemplate->result();
//
//								<cfif HeaderSettingsTemplate.RecordCount NEQ 0>
                if ($HeaderSettingsTemplate_num != 0) {


//									<cfset Caller.OutputMasterKey=HeaderSettingsTemplate.HeaderMaster_Id>
//									<cfoutput>
//									<cfset FontColor="color: #HeaderSettingsTemplate.HeaderColor#;">
//									<cfset FontSize="font-size: #HeaderSettingsTemplate.HeaderSize#;">
//									<cfset FontFace="font-family: #HeaderSettingsTemplate.FontName#;">
                  $OutputMasterKey = $HeaderSettingsTemplate_row->HeaderMaster_ID;
                  $FontColor = $HeaderSettingsTemplate_row->HeaderColor;
                  $FontSize = $HeaderSettingsTemplate_row->HeaderSize;
                  $FontFace = $HeaderSettingsTemplate_row->FontName;
//									<cfif HeaderSettingsTemplate.HeaderStyle Contains "B">
//										<cfset Variables.FontWeight="font-weight: bold;">
//									<cfelse>
//										<cfset Variables.FontWeight="">
//									</cfif>
                  if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'B') !== false) {
                    $FontWeight = "font-weight: bold;";
                  } else {
                    $FontWeight = "";
                  }
//									<cfif HeaderSettingsTemplate.HeaderStyle Contains "I">
//										<cfset Variables.FontStyle="font-style: italic;">
//									<cfelse>
//										<cfset Variables.FontStyle="">
//									</cfif>
                  if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'I') !== false) {
                    $FontStyle = "font-style: italic;";
                  } else {
                    $FontStyle = "";
                  }
//									<cfif HeaderSettingsTemplate.HeaderStyle Contains "U">
//										<cfset Variables.FontDecoration="text-decoration: underline;">
//									<cfelse>
//										<cfset Variables.FontDecoration="">
//									</cfif>
                  if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'U') !== false) {
                    $FontDecoration = "text-decoration: underline;";
                  } else {
                    $FontDecoration = "";
                  }
//									<span style="#Trim(Variables.FontColor)# #Trim(Variables.FontSize)# #Trim(Variables.FontFace)# #Trim(Variables.FontWeight)# #Trim(Variables.FontStyle)# #Variables.FontDecoration#">
//									#Trim(HeaderSettingsTemplate.HeaderText)#<cfif Trim(HeaderSettingsTemplate.HeaderText) NEQ ""><cfif CountTML1IDs.RecordCount GT 1>: #Trim(CountTML1IDs.TML1_Description)#</cfif><br></cfif>
//									</span>
                  $des = "";
                  if ($HeaderSettingsTemplate_row->HeaderText != "") {
                    if ($CountTML1IDs_num > 1) {
                      $des = ':' . $CountTML1IDs_dt->TML1_Description . '<br/>';
                    }
                  }
                  echo '<span style="color:#' . trim($FontColor) . '; font-size:' . trim($FontSize) . 'px;  font-family:' . trim($FontFace) . '; ' . trim($FontWeight) . '  ' . trim($FontStyle) . ' ' . trim($FontDecoration) . '">
									   ' . trim($HeaderSettingsTemplate_row->HeaderText) . '
                     ' . $des . '
									</span>';
//									</cfoutput>
//								</cfif>
                }
//							</cfif>
//						</cfif>
//					</cfif>
              }
            }
          }
//
//					<cfset Variables.Row2Count=1>
//					<cfset Variables.Crlf=chr(13)&chr(10)>
//					<cfif TML.RecordCount NEQ 0>

          $Row2Count = 1;
          $Crlf = "chr(13)&chr(10)";
          if ($TML_num != 0) {
//						<!---
//						The CFC call below returns the Font and Color information for the display of Chart Note body items.
//						Six items are returned:
//
//						Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//						Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//						Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//						Variables.BodyFontInfo.FontWeight = Bold or Normal
//						Variables.BodyFontInfo.FontStyle = Italics or Normal
//						Variables.BodyFontInfo.FontDecoration = Underline or None
//						--->
//						<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getBodyFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.HeaderKey)>
//						<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
            $data['data_db'] = $data_db;
            $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
            $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";


//		                <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//							<tr>
//								<td width="6"></td>
//								<cfoutput>
//								<td align="left" style="width: 7.0in; #variables.DefaultStyle#" valign="top">
//								</cfoutput>
//
//									<cfset variables.CurrentSentence = "">
//									<cfoutput query="TML" group="TML2_Sentence">
//										<cfif Trim(TML.TML2_Sentence) NEQ "" AND Trim(TML.TML3_TextToType) NEQ "">
//											<cfset variables.CurrentSentence = variables.CurrentSentence & Trim(TML.TML2_Sentence) & " ">
//											<cfoutput>
//												<cfif Variables.Row2Count NEQ 1>
//													<cfif Trim(TML.TML3_TextToType) NEQ "[Input]">
//														<cfif (TML.RecordCount EQ TML.CurrentRow) OR (StructFind(TML2Struct,TML.TML2_Sentence) EQ TML.TML3_Id)>
//															<cfset variables.CurrentSentence = variables.CurrentSentence & " and ">
//														<cfelse>
//															<cfset variables.CurrentSentence = variables.CurrentSentence & ", ">
//														</cfif>
//													<cfelse>
//														<cfif StructKeyExists(L3InputStruct,TML.TML3_Id)>
//															<cfif (TML.RecordCount EQ TML.CurrentRow) OR (StructFind(TML2Struct,TML.TML2_Sentence) EQ TML.TML3_Id)>
//																<cfset variables.CurrentSentence = variables.CurrentSentence & " and ">
//															<cfelse>
//																<cfset variables.CurrentSentence = variables.CurrentSentence & ", ">
//															</cfif>
//														</cfif>
//													</cfif>
//												</cfif>
//												<cfif Trim(TML.TML3_TextToType) EQ "[Input]">
//													<cfif StructKeyExists(L3InputStruct,TML.TML3_Id)>
//														<cfset variables.CurrentSentence = variables.CurrentSentence & Trim(StructFind(L3InputStruct,TML.TML3_Id))>
//													</cfif>
//												<cfelse>
//													<cfif FindNoCase("<table", TML.TML3_TextToType) NEQ 0>
//														<cfset variables.CurrentSentence = variables.CurrentSentence & TML.TML3_TextToType>
//													<cfelse>
//														<cfset variables.CurrentSentence = variables.CurrentSentence & ReplaceNoCase(TML.TML3_TextToType,Variables.Crlf,"<br>","ALL")>
//													</cfif>
//												</cfif>
//												<cfset Variables.Row2Count=Variables.Row2Count+1>
//											</cfoutput>
//											<cfset Variables.Row2Count=1>
//											<cfset variables.CurrentSentence = variables.CurrentSentence & ".&nbsp;&nbsp;">
//										</cfif>
//									</cfoutput>
//
//									<cfoutput>
//									#variables.CurrentSentence#
//									</cfoutput>
//
//								</td>
//							</tr>
//						</table>
//					</cfif>
            $CurrentSentence = "";
            // $CurrentSentence2 = "";
            $s_tml2 = array();
            $i = 0;
            foreach ($TML_result as $TML_dt) {
              $i++;

              if (trim($TML_dt->TML2_Sentence) != "" && trim($TML_dt->TML3_TextToType) != "") {

                if (!in_array(trim($TML_dt->TML2_Sentence), $s_tml2)) {
                  $CurrentSentence = $CurrentSentence . trim($TML_dt->TML2_Sentence) . ' ';
                }
                $s_tml2[] = trim($TML_dt->TML2_Sentence);




				 if (trim(strtolower($TML_dt->TML3_TextToType)) != "[input]") {
					if(!empty($TML_dt->TheoAnswer_ID)){
						$CurrentSentence = $CurrentSentence.' <b>'. trim($TML_dt->TML3_TextToType).'</b>, ';
					}
					else{
						$CurrentSentence = $CurrentSentence.' '. ($TML_dt->TML3_TextToType).', ';
					}
                } else {
                  if (array_key_exists($TML_dt->TML3_ID, $L3InputStruct)) {
						$CurrentSentence = $CurrentSentence.' <b>'.trim($L3InputStruct[$TML_dt->TML3_ID]).'</b>, ';

                  }
                }



				if ($i == $TML_num - 1) {
					$CurrentSentence = $CurrentSentence .'  ';
				}



                $Row2Count = $Row2Count + 1;
              }
            }






            $Row2Count = 1;
            $CurrentSentence = $CurrentSentence . ".&nbsp;&nbsp;";

			$CurrentSentence =    str_replace(':,', ':', $CurrentSentence);
			$CurrentSentence =    str_replace(': ,', ':', $CurrentSentence);
			$CurrentSentence =    str_replace(', .', '.', $CurrentSentence);
			$CurrentSentence =    str_replace(':</b>,', ':</b>', $CurrentSentence);
			$CurrentSentence =    str_replace(':</b> ,', ':</b>', $CurrentSentence);
			$CurrentSentence =    preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $CurrentSentence );
			
			

            echo '     <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
							<tr>
								<td width="6"></td>

								<td align="left" style="width: 7.0in; ' . $DefaultStyle . ' valign="top">

									' . $CurrentSentence . '

								</td>
							</tr>
						</table>';
          }
//
//				</cfloop>
        }
      } else {
//			<cfelse>
//		        <cfquery datasource="#Attributes.TemplateDataSource#" name="TML">
//				Select T2.TML2_Id,
//				       T2.TML2_Sentence,
//					   T3.TML3_TextToType,
//					   T3.TML3_Id
//				  From #Attributes.TemplateDataSource#.dbo.TML2 T2
//				  JOIN #Attributes.TemplateDataSource#.dbo.TML3 T3
//				    ON T2.TML2_ID=T3.TML2_Id
//				  JOIN #Attributes.TemplateDataSource#.dbo.TML1 T1
//				    ON T2.TML1_ID=T1.TML1_Id
//	     		 Where T2.TML2_HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderMasterKey#">
//				   And T2.TML2_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL2IDS.TML2_Id,',')#">)
//				   And T3.TML3_Id IN (<cfqueryparam list="Yes" separator="," value="#ValueList(ETL3IDS.TML3_Id,',')#">)
//				   And (T2.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T2.Hidden IS NULL)
//				   And (T3.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T3.Hidden IS NULL)
//				   And (T1.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR T1.Hidden IS NULL)
//				   Order By T1.Sequence, T2.Sequence, T3.Sequence
//				</cfquery>
        $abnormal = (isset($summary_report) ? 'And T3.Abnormal = 1  ' : "");
        $sql = "Select T2.TML2_ID,
				       T2.TML2_Sentence,
					   T3.TML3_TextToType,
					   T3.TML3_ID,
             T3.TheoQuestion_ID,
             T3.TheoAnswer_ID
				  From $template_db.dbo.TML2 T2
				  JOIN $template_db.dbo.TML3 T3
				    ON T2.TML2_ID=T3.TML2_ID
				  JOIN $template_db.dbo.TML1 T1
				    ON T2.TML1_ID=T1.TML1_ID
	     		 Where T2.TML2_HeaderMaster_ID=$HeaderMasterKey
				   And T2.TML2_ID IN (" . implode(',', $ETL2IDS_arr) . ")
				   And T3.TML3_ID IN (" . implode(',', $ETL3IDS_arr) . ")
				   And (T2.Hidden<>1 OR T2.Hidden IS NULL)
				   And (T3.Hidden<>1 OR T3.Hidden IS NULL)
				   And (T1.Hidden<>1 OR T1.Hidden IS NULL)
           $abnormal
				   Order By T1.Sequence, T2.Sequence, T3.Sequence";
        $TML = $this->ReportModel->data_db->query($sql);
        $TML_num = $TML->num_rows();
        $TML_row = $TML->row();
        $TML_result = $TML->result();
//				<cfset TML2Struct=StructNew()>
//				<cfloop query="TML">
//					<cfset Temp=StructInsert(TML2Struct,TML.TML2_Sentence,TML.TML3_Id,TRUE)>
//				</cfloop>

        $TML2Struct = array();
        foreach ($TML_result as $TML_dt) {
          $TML2Struct["$TML_dt->TML2_Sentence"] = $TML_dt->TML3_ID;
        }
//				<cfif TML.RecordCount NEQ 0>
//					<cfif Attributes.OutPutMasterKey NEQ Attributes.HeaderMasterKey>
        if ($TML_num != 0) {
          if ($OutPutMasterKey != $HeaderMasterKey) {

//<!---
//						<cfif caller.HeaderNeeded EQ True>
//							<cfmodule template="componentheaders.cfm"
//							 EMRDataSource="#Attributes.EMRDataSource#"
//							 HeaderKey="#Attributes.HeaderKey#"
//							 PatientKey="#Attributes.PatientKey#"
//							 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//							 FreeTextKey="#Attributes.FreeTextKey#"
//							 SOHeaders="#Attributes.SOHeaders#">
//							<cfset caller.HeaderNeeded = False>
//						</cfif>
//--->
//<!--- --->
//						<cfif caller.NeedTemplateHeader EQ True>

            if ($NeedTemplateHeader == TRUE) {
//							<cfquery datasource="#Attributes.EMRDataSource#" name="HeaderSettingsTemplate" maxrows="1">
//							Select TOP 1
//							       H.Header_Id,
//							       H.HeaderMaster_Id,
//							       H.HeaderText,
//								   H.HeaderStyle,
//								   H.HeaderSize,
//								   H.HeaderColor,
//								   F.FontName
//							  From EncounterHeaders H
//							  Join Fonts F
//							    On H.Font_Id=F.Font_Id
//							 Where H.HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderMasterKey#">
//							   And H.Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ProviderKey#">
//	   						   And H.EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.EncounterDescriptionKey#">
//							   And (H.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR H.Hidden IS NULL)
//							</cfquery>
//

              $sql = "							Select TOP 1
							       H.Header_ID,
							       H.HeaderMaster_ID,
							       H.HeaderText,
								   H.HeaderStyle,
								   H.HeaderSize,
								   H.HeaderColor,
								   F.FontName
							  From EncounterHeaders H
							  Join Fonts F
							    On H.Font_ID=F.Font_ID
							 Where H.HeaderMaster_ID=$HeaderMasterKey
							   And H.Provider_ID=$ProviderKey
	   						   And H.EncounterDescription_ID=$EncounterDescriptionKey
							   And (H.Hidden<>1 OR H.Hidden IS NULL)";
              $HeaderSettingsTemplate = $this->ReportModel->data_db->query($sql);
              $HeaderSettingsTemplate_num = $HeaderSettingsTemplate->num_rows();
              $HeaderSettingsTemplate_row = $HeaderSettingsTemplate->row();
              $HeaderSettingsTemplate_result = $HeaderSettingsTemplate->result();

//							<cfif HeaderSettingsTemplate.RecordCount NEQ 0>
              if ($HeaderSettingsTemplate_num != 0) {
//								<cfset Caller.OutputMasterKey=HeaderSettingsTemplate.HeaderMaster_ID>
//								<cfoutput>
//								<cfset FontColor="color: #HeaderSettingsTemplate.HeaderColor#;">
//								<cfset FontSize="font-size: #HeaderSettingsTemplate.HeaderSize#;">
//								<cfset FontFace="font-family: #HeaderSettingsTemplate.FontName#;">

                $OutputMasterKey = $HeaderSettingsTemplate_row->HeaderMaster_ID;
                $FontColor = $HeaderSettingsTemplate_row->HeaderColor;
                $FontSize = $HeaderSettingsTemplate_row->HeaderSize;
                $FontFace = $HeaderSettingsTemplate_row->FontName;
//								<cfif HeaderSettingsTemplate.HeaderStyle Contains "B">
//									<cfset Variables.FontWeight="font-weight: bold;">
//								<cfelse>
//									<cfset Variables.FontWeight="">
//								</cfif>
                if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'B') !== false) {
                  $FontWeight = "font-weight: bold;";
                } else {
                  $FontWeight = "";
                }
//								<cfif HeaderSettingsTemplate.HeaderStyle Contains "I">
//									<cfset Variables.FontStyle="font-style: italic;">
//								<cfelse>
//									<cfset Variables.FontStyle="">
//								</cfif>
                if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'I') !== false) {
                  $FontStyle = "font-style: italic;";
                } else {
                  $FontStyle = "";
                }
//								<cfif HeaderSettingsTemplate.HeaderStyle Contains "U">
//									<cfset Variables.FontDecoration="text-decoration: underline;">
//								<cfelse>
//									<cfset Variables.FontDecoration="">
//								</cfif>
                if (strpos($HeaderSettingsTemplate_row->HeaderStyle, 'U') !== false) {
                  $FontDecoration = "text-decoration: underline;";
                } else {
                  $FontDecoration = "";
                }
//								<span style="#Trim(Variables.FontColor)# #Trim(Variables.FontSize)# #Trim(Variables.FontFace)# #Trim(Variables.FontWeight)# #Trim(Variables.FontStyle)# #Variables.FontDecoration#">
//								#Trim(HeaderSettingsTemplate.HeaderText)#
//								<cfif Trim(HeaderSettingsTemplate.HeaderText) NEQ ""><br></cfif>
//								</span>
//								</cfoutput>
                $des = "";
                if ($HeaderSettingsTemplate_row->HeaderText != "") {
                  $des = '<br/>';
                }
                echo '<span style="color:#' . trim($FontColor) . '; font-size:' . trim($FontSize) . 'px;  font-family:' . trim($FontFace) . '; ' . trim($FontWeight) . '  ' . trim($FontStyle) . ' ' . trim($FontDecoration) . '">
									   ' . trim($HeaderSettingsTemplate_row->HeaderText) . '
                     ' . $des . '
									</span>';
//							</cfif>
//						</cfif>
              }
            }
//<!--- --->
//					</cfif>
//				</cfif>
          }
        }
//
//				<cfset Variables.Row2Count=1>
//				<cfset Variables.Crlf=chr(13)&chr(10)>
//				<cfif TML.RecordCount NEQ 0>
        $Row2Count = 1;
        $Crlf = "chr(13)&chr(10)";
        if ($TML_num != 0) {
//					<!---
//					The CFC call below returns the Font and Color information for the display of Chart Note body items.
//					Six items are returned:
//
//					Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//					Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//					Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//					Variables.BodyFontInfo.FontWeight = Bold or Normal
//					Variables.BodyFontInfo.FontStyle = Italics or Normal
//					Variables.BodyFontInfo.FontDecoration = Underline or None
//					--->
//					<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getBodyFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.HeaderKey)>
//					<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
          $data['data_db'] = $data_db;
          $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
          $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";

//	                <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//						<tr>
//							<td width="6"></td>
//							<cfoutput>
//							<td align="left" style="width: 7.0in; #variables.DefaultStyle#" valign="top">
//							</cfoutput>
//
//								<cfset variables.CurrentSentence = "">
//								<cfoutput query="TML" group="TML2_Sentence">
//									<cfif Trim(TML.TML2_Sentence) NEQ "" AND Trim(TML.TML3_TextToType) NEQ "">
//										<cfset variables.CurrentSentence = variables.CurrentSentence & Trim(TML.TML2_Sentence) & " ">
//										<cfoutput>
//											<cfif Variables.Row2Count NEQ 1>
//												<cfif Trim(TML.TML3_TextToType) NEQ "[Input]">
//													<cfif (TML.RecordCount EQ TML.CurrentRow) OR (StructFind(TML2Struct,TML.TML2_Sentence) EQ TML.TML3_Id)>
//														<cfset variables.CurrentSentence = variables.CurrentSentence & " and ">
//													<cfelse>
//														<cfset variables.CurrentSentence = variables.CurrentSentence & ", ">
//													</cfif>
//												<cfelse>
//													<cfif StructKeyExists(L3InputStruct,TML.TML3_Id)>
//														<cfif (TML.RecordCount EQ TML.CurrentRow) OR (StructFind(TML2Struct,TML.TML2_Sentence) EQ TML.TML3_Id)>
//															<cfset variables.CurrentSentence = variables.CurrentSentence & " and ">
//														<cfelse>
//															<cfset variables.CurrentSentence = variables.CurrentSentence & ", ">
//														</cfif>
//													</cfif>
//												</cfif>
//											</cfif>
//											<cfif Trim(TML.TML3_TextToType) EQ "[Input]">
//												<cfif StructKeyExists(L3InputStruct,TML.TML3_Id)>
//													<cfset variables.CurrentSentence = variables.CurrentSentence & Trim(StructFind(L3InputStruct,TML.TML3_Id))>
//												</cfif>
//											<cfelse>
//												<cfif FindNoCase("<table", TML.TML3_TextToType) NEQ 0>
//													<cfset variables.CurrentSentence = variables.CurrentSentence & TML.TML3_TextToType>
//												<cfelse>
//													<cfset variables.CurrentSentence = variables.CurrentSentence & ReplaceNoCase(TML.TML3_TextToType,Variables.Crlf,"<br>","ALL")>
//												</cfif>
//											</cfif>
//											<cfset Variables.Row2Count=Variables.Row2Count+1>
//										</cfoutput>
//										<cfset Variables.Row2Count=1>
//										<cfset variables.CurrentSentence = variables.CurrentSentence & ".&nbsp;&nbsp;">
//									</cfif>
//								</cfoutput>
//
//								<cfoutput>
//								#variables.CurrentSentence#
//								</cfoutput>
//
//							</td>
//						</tr>
//					</table>
//				</cfif>
//			</cfif>
//		</cfif>
//	</cfif>
//</cfif>

          $CurrentSentence = "";
          // $CurrentSentence2 = "";
          $s_tml2 = array();
          $s_theo = '';
          $i = 0;
          foreach ($TML_result as $TML_dt) {
            $i++;

            if (trim($TML_dt->TML2_Sentence) != "" && trim($TML_dt->TML3_TextToType) != "") {

              if (!in_array(trim($TML_dt->TML2_Sentence), $s_tml2)) {
                $CurrentSentence = $CurrentSentence . trim($TML_dt->TML2_Sentence) . ' ';
              }
              $s_tml2[] = trim($TML_dt->TML2_Sentence);
              if (trim(strtolower($TML_dt->TML3_TextToType)) != "[input]") {
					if(!empty($TML_dt->TheoAnswer_ID)){
						$CurrentSentence = $CurrentSentence.' <b>'. trim($TML_dt->TML3_TextToType).'</b>, ';
					}
					else{
						$CurrentSentence = $CurrentSentence.' '. trim($TML_dt->TML3_TextToType).', ';
					}
                } else {
                  if (array_key_exists($TML_dt->TML3_ID, $L3InputStruct)) {
						$CurrentSentence = $CurrentSentence.' <b>'.trim($L3InputStruct[$TML_dt->TML3_ID]).'</b>, ';

                  }
                }



				if ($i == $TML_num - 1) {
					$CurrentSentence = $CurrentSentence .'  ';
				}




              $Row2Count = $Row2Count + 1;
            }
          }




          $Row2Count = 1;
          if (!empty($CurrentSentence)) {
            $CurrentSentence = $CurrentSentence . ".&nbsp;&nbsp;";
          }
			
         $CurrentSentence =    str_replace(':,', ':', $CurrentSentence);
		 $CurrentSentence =    str_replace(': ,', ':', $CurrentSentence);
		 $CurrentSentence =    str_replace(', .', '.', $CurrentSentence);
		 $CurrentSentence =    str_replace(':</b>,', ':</b>', $CurrentSentence);
		 $CurrentSentence =    str_replace(':</b> ,', ':</b>', $CurrentSentence);
		 $CurrentSentence =    preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $CurrentSentence );

          echo '     <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
							<tr>
								<td width="6"></td>

								<td align="left" style="width: 7.0in; ' . $DefaultStyle . ' valign="top">

									' . $CurrentSentence . '

								</td>
							</tr>
						</table>';
        }
      }
    }
  }
}
?>

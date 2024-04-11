<?php
//<!--- CASE 10,018  CH  21 July 2011 --->
//<!--- CASE 8647 - Rewrote query and display to choose between using vaccines and immunizationsmaster  --->
//
//
//<!---CASE 10,018 Added For Reviewed by info--->
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.orgTimeZoneOffset = Session.UTC_TimeOffset>
//	<cfset Variables.orgTimeZoneDST = Session.UTC_DST>
//	<cfset Variables.orgTimeZoneId = Session.UTC_TimeZoneId>
//</cflock>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="AllergyNotes2">
//	Select	Allergy_ID,
//			isNull(AllergyDescription, '') as AllergyDescription,
//			AllergyType,
//			<!---CASE 10,018 Add patient_id--->
//			Patient_Id,
//			MedicationMaster_ID,
//			AllergyDate,
//			Removed_By,
//			CASE WHEN AllergyType = 2 THEN
//					(
//					 SELECT TOP 1 MedicationName
//					 FROM MedicationMaster M
//					 WHERE A.MedicationMaster_ID = M.MedicationMaster_ID
//					)
//					
//					WHEN AllergyType = 4 AND isnull(ImmunizationsMaster_Id, 0) > 0 THEN
//					(
//						 SELECT TOP 1 Immunization as VaccineName
//						   FROM ImmunizationsMaster
//						  WHERE A.ImmunizationsMaster_Id = ImmunizationsMaster_ID
//					)
//						
//					WHEN AllergyType = 4 THEN
//						(
//						 SELECT TOP 1 VaccineName
//						 FROM Vaccines V
//						 WHERE A.MedicationMaster_ID = V.Vaccine_Id
//						)
//						
//					WHEN AllergyType = 5 AND A.SubClassID < 1 THEN
//						(
//						 SELECT TOP 1 Class_Description
//						 FROM Multum.dbo.alr_class MC
//						 WHERE A.ClassID = MC.Class_Id
//						)   
//						
//					WHEN AllergyType = 5 AND A.SubClassID >= 1 THEN
//						(
//						 SELECT TOP 1  Class_Description + '/' + category_description_plural
//						 FROM  Multum.dbo.alr_class MC 
//						 JOIN  Multum.dbo.alr_category_class_map MP
//						 ON  MC.class_id = MP.class_Id
//                         JOIN  Multum.dbo.alr_category AC
//						 ON  MP.alr_category_id = AC.alr_category_id
//						 WHERE (A.ClassID = MC.Class_Id)
//						 AND (A.SubClassID = AC.alr_category_Id)
//						)   
//					ELSE
//						(
//						 AllergyDescription						 
//						)
//					END AS AllergyTypeDetails,
//					
//
//					CASE WHEN AllergyType = 4 AND isnull(ImmunizationsMaster_Id, 0) > 0 THEN
//					(
//						 SELECT '' As Abbreviation
//					)
//
//					WHEN AllergyType = 4 THEN
//						(
//							SELECT TOP 1 Abbreviation
//							 FROM Vaccines V
//							 WHERE A.MedicationMaster_ID = V.Vaccine_Id
//						)
//					END AS Abbreviation
//	From 	Allergy as A
//	Where	A.Allergy_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//			And (A.Deleted<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR A.Deleted IS NULL)			 
//	Order By	AllergyDate DESC, Allergy_ID DESC
//</cfquery>

$sql = "	Select	Allergy_ID,
			isNull(AllergyDescription, '') as AllergyDescription,
			AllergyType,
			Patient_Id,
			MedicationMaster_ID,
			AllergyDate,
			Removed_By,
			CASE WHEN AllergyType = 2 THEN
					(
					 SELECT TOP 1 MedicationName
					 FROM MedicationMaster M
					 WHERE A.MedicationMaster_ID = M.MedicationMaster_ID
					)
					
					WHEN AllergyType = 4 AND isnull(ImmunizationsMaster_Id, 0) > 0 THEN
					(
						 SELECT TOP 1 Immunization as VaccineName
						   FROM ImmunizationsMaster
						  WHERE A.ImmunizationsMaster_Id = ImmunizationsMaster_ID
					)
						
					WHEN AllergyType = 4 THEN
						(
						 SELECT TOP 1 VaccineName
						 FROM Vaccines V
						 WHERE A.MedicationMaster_ID = V.Vaccine_Id
						)
						
					WHEN AllergyType = 5 AND A.SubClassID < 1 THEN
						(
						 SELECT TOP 1 Class_Description
						 FROM Multum.dbo.alr_class MC
						 WHERE A.ClassID = MC.Class_Id
						)   
						
					WHEN AllergyType = 5 AND A.SubClassID >= 1 THEN
						(
						 SELECT TOP 1  Class_Description + '/' + category_description_plural
						 FROM  Multum.dbo.alr_class MC 
						 JOIN  Multum.dbo.alr_category_class_map MP
						 ON  MC.class_id = MP.class_Id
                         JOIN  Multum.dbo.alr_category AC
						 ON  MP.alr_category_id = AC.alr_category_id
						 WHERE (A.ClassID = MC.Class_Id)
						 AND (A.SubClassID = AC.alr_category_Id)
						)   
					ELSE
						(
						 AllergyDescription						 
						)
					END AS AllergyTypeDetails,
					

					CASE WHEN AllergyType = 4 AND isnull(ImmunizationsMaster_Id, 0) > 0 THEN
					(
						 SELECT '' As Abbreviation
					)

					WHEN AllergyType = 4 THEN
						(
							SELECT TOP 1 Abbreviation
							 FROM Vaccines V
							 WHERE A.MedicationMaster_ID = V.Vaccine_Id
						)
					END AS Abbreviation
	From 	" . $data_db . ".dbo.Allergy as A
	Where	A.Allergy_Id In ($ComponentKey)
			And (A.Deleted<>1 OR A.Deleted IS NULL)			 
	Order By	AllergyDate DESC, Allergy_ID DESC";


$AllergyNotes2 = $this->ReportModel->data_db->query($sql);
$AllergyNotes2_num = $AllergyNotes2->num_rows();
$AllergyNotes2_row = $AllergyNotes2->row();

//<!---CASE 10,018 Added For Reviewed by info--->
//<cfif AllergyNotes2.RecordCount neq 0>
//	<cfquery datasource="#Attributes.EMRDataSource#" name="GetReviewed">
//		Select	TOP 1
//				dbo.UTCtoLocaltz(P.AllergiesReviewedDate_UTC,<cfqueryparam cfsqltype="cf_sql_numeric" scale="2" value="#Variables.orgTimeZoneOffset#">,<cfqueryparam cfsqltype="cf_sql_bit" value="#Variables.orgTimeZoneDST#">,<cfqueryparam cfsqltype="cf_sql_numeric" scale="2" value="#Variables.orgTimeZoneId#">) as ReviewedOn_UTC,
//				AllergiesReviewedBy,
//				isNull(PP.ProviderTitle,'') + ' ' + u.FName + ' ' + u.LName + ' ' As FullName
//		FROM	PatientProfile P
//				INNER JOIN #Attributes.DSNPreFix#eCast_Data.dbo.Users U
//	        	    	ON P.AllergiesReviewedBy=u.Id
//		        LEFT JOIN ProviderProfile PP
//		            	ON u.User_Id=PP.User_Id
//		 Where P.Patient_Id=<cfqueryparam cfsqltype="cf_sql_bigint" value="#AllergyNotes2.Patient_Id#">
//	</cfquery>
//</cfif>

if ($AllergyNotes2_num > 0) {
  //SKIP UTCtoLocaltz
  $sql = "Select	TOP 1
				P.AllergiesReviewedDate_UTC as ReviewedOn_UTC,
				AllergiesReviewedBy,
				isNull(PP.ProviderTitle,'') + ' ' + u.FName + ' ' + u.LName + ' ' As FullName
		FROM	" . $data_db . ".dbo.PatientProfile P
				INNER JOIN " . $user_db . ".dbo.Users U
	        	    	ON P.AllergiesReviewedBy=u.Id
		        LEFT JOIN " . $data_db . ".dbo.ProviderProfile PP
		            	ON u.User_Id=PP.User_Id
		 Where P.Patient_Id=$AllergyNotes2_row->Patient_Id";

  $GetReviewed = $this->ReportModel->data_db->query($sql);
  $GetReviewed_num = $GetReviewed->num_rows();
  $GetReviewed_row = $GetReviewed->row();
}

//<cfif AllergyNotes2.RecordCount NEQ 0>
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
//
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;"> 
//		<!---CASE 10,018 Added Reviewed by info--->
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td colspan="5">
//				<cfoutput>
//					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
//					<font style="#variables.DefaultStyle#">
//						(Last Reviewed By: 
//						<cfif GetReviewed.Recordcount neq 0>
//							#GetReviewed.FullName#
//							on
//							#DateFormat(GetReviewed.ReviewedOn_UTC,"MM/DD/YYYY")# #TimeFormat(GetReviewed.ReviewedOn_UTC,"h:mm tt")#
//						</cfif>
//						)
//					</font>
//				</cfoutput>
//			</td>
//		</tr>
//
//		<cfoutput query="AllergyNotes2">		
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top" nowrap>
//					#DateFormat(AllergyNotes2.AllergyDate,"mm/dd/yyyy")#
//				</td>
//				<td width="1">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top" nowrap>
//					<cfif AllergyNotes2.AllergyType EQ 0>
//						No Known Allergies
//					<cfelseif AllergyNotes2.AllergyType EQ 6>
//						No Known Medication Allergies
//					<cfelseif AllergyNotes2.AllergyType EQ 1>
//						Food
//					<cfelseif AllergyNotes2.AllergyType EQ 2>	
//						Medication
//					<cfelseif AllergyNotes2.AllergyType EQ 3>
//						Other
//					<cfelseif AllergyNotes2.AllergyType EQ 4>	
//						Vaccine
//					<cfelseif AllergyNotes2.AllergyType EQ 5>	
//						Medication Class
//					</cfif>	
//				</td>
//				<td width="4">&nbsp;</td>	
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					<cfif AllergyNotes2.AllergyType EQ 2>	
//						<cfif AllergyNotes2.MedicationMaster_id eq 0>
//							**NDI** 
//						</cfif>	
//					</cfif>
//					
//					<cfif (AllergyNotes2.AllergyType EQ 4) AND (#Trim(AllergyNotes2.Abbreviation)# neq '') >
//						#Left(Trim(AllergyNotes2.Abbreviation),100)# 
//					<cfelse>
//						#Left(Trim(AllergyNotes2.AllergyTypeDetails),100)# 						
//					</cfif>
//					
//					<cfif (Trim(AllergyNotes2.AllergyDescription) neq '') AND (AllergyNotes2.AllergyType NEQ 1) AND (AllergyNotes2.AllergyType NEQ 3) >						
//						(#Left(Trim(AllergyNotes2.AllergyDescription),100)#)
//					</cfif>
//
//					<cfif AllergyNotes2.Removed_By GT 0>(Removed)</cfif>
//				</td>
//			</tr>
//		</cfoutput>			
//	</table>			
//
//</cfif>
//


if ($AllergyNotes2_num > 0) {

//  if (HeaderNeeded) { //SKIPP
  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
//  }
  $DefaultStyle = ""; //SKIPP 
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;"> 
    <!---CASE 10,018 Added Reviewed by info--->
    <tr>
      <td width="7">&nbsp;</td>
      <td colspan="5">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
        <font style="<?php echo $DefaultStyle; ?>">
        (Last Reviewed By: 
        <?php
        if ($GetReviewed_num > 0) {
          echo $GetReviewed_row->FullName . " on ";
//          #DateFormat(GetReviewed.ReviewedOn_UTC,"MM/DD/YYYY")# #TimeFormat(GetReviewed.ReviewedOn_UTC,"h:mm tt")#
           echo date('m-d-Y H:i s', strtotime($AllergyNotes2_row->ReviewedOn_UTC));
        }
        ?>
        )
        </font>
      </td>
    </tr>


    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top" nowrap>
        <!--#DateFormat(AllergyNotes2.AllergyDate,"mm/dd/yyyy")#-->
        <?php echo date('m-d-Y', strtotime($AllergyNotes2_row->AllergyDate)); ?>
      </td>
      <td width="1">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top" nowrap>

        <?php
        if ($AllergyNotes2_row->AllergyType == 0) {
          echo "No Known Allergies";
        } else if ($AllergyNotes2_row->AllergyType == 6) {
          echo "No Known Medication Allergies";
        } else if ($AllergyNotes2_row->AllergyType == 1) {
          echo "Food";
        } else if ($AllergyNotes2_row->AllergyType == 2) {
          echo "Medication";
        } else if ($AllergyNotes2_row->AllergyType == 3) {
          echo "Other";
        } else if ($AllergyNotes2_row->AllergyType == 4) {
          echo "Vaccine";
        } else if ($AllergyNotes2_row->AllergyType == 5) {
          echo "Medication Class";
        }
        ?>
      </td>
      <td width="4">&nbsp;</td>	
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php
        if ($AllergyNotes2_row->AllergyType == 2 && $AllergyNotes2_row->MedicationMaster_id == 0) {
          echo "**NDI**";
        }

        if ($AllergyNotes2_row->AllergyType == 4 && trim($AllergyNotes2_row->Abbreviation) != "") {
          echo substr(trim($AllergyNotes2_row->Abbreviation), 0, 100);
        } else {
          echo substr(trim($AllergyNotes2_row->AllergyTypeDetails), 0, 100);
        }

        if (trim($AllergyNotes2_row->AllergyDescription) != "" && $AllergyNotes2_row->AllergyType != 1 && $AllergyNotes2_row->AllergyType != 3) {
          echo substr(trim($AllergyNotes2_row->AllergyDescription), 0, 100);
        }

        if ($AllergyNotes2_row->AllergyDescription > 0) {
          echo "(Removed)";
        }
        ?>

      </td>
    </tr>
  </table>	
  <?php
}
?>

<!--- 
//
//ORIGINAL CODE
//
//<cfset Variables.AllergyMedList="">
//<cfset Variables.AllergyVacList="">
//
//<cfset ClassIdStruct=StructNew()>
//<cfset SubClassIdStruct=StructNew()>
//<cfset MedNameStruct=StructNew()>	
//<cfset ClassStruct=StructNew()>
//<cfset SubClassStruct=StructNew()>
//<cfset VacNameStruct=StructNew()>
//<cfset VaccineStruct=StructNew()>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="AllergyNotes">
//Select A.Provider_Id,
//	   A.MedicationMaster_Id,
//	   A.Removed_By,
//       A.Allergy_Id,
//       A.ClassId,
//	   A.SubClassId,
//       A.AllergyDate,
//	   A.AllergyType,
//	   A.AllergyDescription,
//       M.MedicationName,
//       V.VaccineName
//  From Allergy A
//  LEFT JOIN MedicationMaster M 
//    ON A.MedicationMaster_Id=M.MedicationMaster_Id
//  LEFT JOIN Vaccines V
//    ON A.MedicationMaster_Id=V.Vaccine_Id
// Where A.Allergy_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
// Order By A.AllergyDate Desc
//</cfquery>
//
//<cfloop query="AllergyNotes">
//	<cfif AllergyNotes.AllergyType EQ 2>
//		<cfset Variables.AllergyMedList=AllergyMedList&AllergyNotes.MedicationMaster_Id&",">
//	<cfelseif AllergyNotes.AllergyType EQ 4>
//		<cfset Variables.AllergyVacList=AllergyVacList&AllergyNotes.MedicationMaster_Id&",">
//	</cfif>
//
//	<cfset Temp=StructInsert(ClassIdStruct,AllergyNotes.ClassId,AllergyNotes.ClassId,TRUE)>
//	<cfif AllergyNotes.SubClassId GT 0>
//		<cfset Temp=StructInsert(SubClassIdStruct,AllergyNotes.SubClassId,AllergyNotes.SubClassId,TRUE)>
//	</cfif>
//</cfloop>
//
//<cfif Variables.AllergyMedList NEQ "">	
//	<cfset Variables.AllergyMedListLength=Len(Variables.AllergyMedList)>
//	<cfset Variables.AllergyMedList=Left(Variables.AllergyMedList,Variables.AllergyMedListLength-1)>
//	
//	<cfquery datasource="#Attributes.EMRDataSource#" name="MedName">
//	Select MedicationMaster_Id,
//	       MedicationName
//	  From MedicationMaster 
//	 Where MedicationMaster_Id IN (<cfqueryparam list="Yes" separator="," value="#AllergyMedList#">)
//	</cfquery>
//	
//	<cfloop query="MedName">
//		<cfset Temp=StructInsert(MedNameStruct,MedName.MedicationMaster_Id,MedName.MedicationName,TRUE)>
//	</cfloop>	
//</cfif>
//
//<cfif Variables.AllergyVacList NEQ "">	
//	<cfset Variables.AllergyVacListLength=Len(Variables.AllergyVacList)>
//	<cfset Variables.AllergyVacList=Left(Variables.AllergyVacList,Variables.AllergyVacListLength-1)>
//	
//	<cfquery datasource="#Attributes.EMRDataSource#" name="VacName">
//	Select Vaccine_Id,
//	       Abbreviation,
//		   VaccineName
//	  From Vaccines
//	 Where Vaccine_Id IN (<cfqueryparam list="Yes" separator="," value="#AllergyVacList#">)
//	</cfquery> 	
//	
//	<cfloop query="VacName">
//		<cfset Variables.Temp=StructInsert(VacNameStruct,VacName.Vaccine_Id,VacName.Abbreviation,TRUE)>
//		<cfset Variables.Temp=StructInsert(VaccineStruct,VacName.Vaccine_Id,VacName.VaccineName,TRUE)>
//	</cfloop>	
//</cfif>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="Class">
//Select Class_Id,
//       Class_Description
//  From Multum.dbo.alr_class 
//  Where Class_ID IN (<cfif StructCount(ClassIdStruct) GT 0><cfqueryparam list="Yes" separator="," value="#StructKeyList(ClassIdStruct,',')#"><cfelse><cfqueryparam cfsqltype="CF_SQL_BIGINT" value="0"></cfif>)
//Order By Class_Description
//</cfquery>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="SubClass">
//Select M.Alr_Category_Id,
//	   C.Category_Description_Plural
//  From Multum.dbo.Alr_Category_Class_Map M 
//  LEFT JOIN Multum.dbo.Alr_Category C 
//    ON M.Alr_Category_Id=C.Alr_Category_Id
// Where C.Alr_Category_Id IN(<cfif StructCount(SubClassIdStruct) GT 0><cfqueryparam list="Yes" separator="," value="#StructKeyList(SubClassIdStruct,',')#"><cfelse><cfqueryparam cfsqltype="CF_SQL_BIGINT" value="0"></cfif>)
//</cfquery>
//
//<cfif Class.RecordCount NEQ 0>
//	<cfloop query="Class">
//		<cfset Temp=StructInsert(ClassStruct,Trim(Class.Class_Id),Trim(Class.Class_Description),TRUE)>
//	</cfloop>
//</cfif>
//
//<cfif SubClass.RecordCount NEQ 0>
//	<cfloop query="SubClass">
//		<cfset Temp=StructInsert(SubClassStruct,Trim(SubClass.Alr_Category_Id),Trim(SubClass.Category_Description_Plural),TRUE)>
//	</cfloop>
//</cfif>
//
//<cfif AllergyNotes.RecordCount NEQ 0>
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
//
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//	<cfoutput query="AllergyNotes">
//	<tr>
//	<td width="7"></td>
//	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//	#DateFormat(AllergyNotes.AllergyDate,"mm/dd/yyyy")#
//	</td>
//	<td width="4"></td>
//	<td colspan="4" align="left" nowrap style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//		<cfset Variables.Description=AllergyNotes.AllergyDescription>
//		<cfset Variables.AllergyTypeText="">
//		<cfif AllergyNotes.AllergyType EQ 0>
//		    <cfset Variables.AllergyTypeText="(No Known Allergies)">
//		<cfelseif AllergyNotes.AllergyType EQ 1>
//			<cfset Variables.AllergyTypeText="(Food)">
//		<cfelseif AllergyNotes.AllergyType EQ 2>
//			<cfif IsStruct(MedNameStruct)>
//				<cfif StructKeyExists(MedNameStruct,AllergyNotes.MedicationMaster_Id)>
//					<cfset Variables.AllergyTypeText="(Medication (#StructFind(MedNameStruct,AllergyNotes.MedicationMaster_Id)#))"> 
//				<cfelse>
//					<cfset Variables.AllergyTypeText="(Medication)"> 
//					<cfset Variables.Description="**NDI** "&Variables.Description>
//				</cfif>
//			</cfif>
//		<cfelseif AllergyNotes.AllergyType EQ 3>
//			<cfset Variables.AllergyTypeText="(Other)">
//		<cfelseif AllergyNotes.AllergyType EQ 4>
//			<cfif IsStruct(VaccineStruct)>
//				<cfif StructKeyExists(VacNameStruct,AllergyNotes.MedicationMaster_Id)>
//				    <cfset Variables.AllergyTypeText="(Vaccine (#StructFind(VacNameStruct,AllergyNotes.MedicationMaster_Id)#))"> 
//				</cfif>
//			</cfif>
//		<cfelseif AllergyNotes.AllergyType EQ 5>
//			<cfif StructKeyExists(ClassStruct,AllergyNotes.ClassId)>
//				<cfset Variables.AllergyTypeText="(Medication Class - #StructFind(ClassStruct,AllergyNotes.ClassId)#"> 
//				<cfif StructKeyExists(SubClassStruct,AllergyNotes.SubClassId)>
//					<cfset Variables.AllergyTypeText=" #Variables.AllergyTypeText# (#StructFind(SubClassStruct,AllergyNotes.SubClassId)#)">
//				</cfif>
//				<cfset Variables.AllergyTypeText="#Variables.AllergyTypeText#)">
//			</cfif>		
//		</cfif>
//		#Left(Variables.Description,100)# #Left(Variables.AllergyTypeText,100)# <cfif AllergyNotes.Removed_By GT 0>(Removed)</cfif>
//	</td>
//	<td width="4"></td>
//	</tr>
//	</cfoutput>
//	</table>
//</cfif>
//
//--->

<?php
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sPatientId=Session.Patient_Id>
//	<cfset Variables.sOrgId=Session.Org_Id>
//</cflock>
//
//<cfquery datasource="#Variables.EMRDataSource#" name="PatientProfile">
//Select TOP 1 *
//  From PatientProfile
// Where <cfif IsDefined('Url.Patient_Id')>Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.Patient_Id#"><cfelse>Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Variables.sPatientId#"></cfif>
// AND Org_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Variables.sOrgId#">
//</cfquery>
//
//<cfif PatientProfile.DOB NEQ "">
//	<cfset Variables.Years=DateDiff("yyyy", PatientProfile.DOB,Now())>
//<cfelse>
//	<cfset Variables.Years=0>
//</cfif>
//

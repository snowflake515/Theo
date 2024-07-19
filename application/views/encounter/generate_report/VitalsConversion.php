<?php
//
//<!--- Program Name: vitalsconversion.cfm
//
//NOTE: This file is shared between the EMR/SYSTEM and EMR folders
//
//--->
//
//<cfset variables.VitalsConversionLoaded = 1>
//
//<cffunction name="cmORinch" output="no" returntype="string">
//	<cfargument name="Value_cm" type="string" required="yes">
//	<cfargument name="EngMetric" type="boolean" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Trim(Value_cm) EQ "")>
//		<cfset variables.RetValue = "">
//	<cfelseif EngMetric EQ 0>	
//		<cfset variables.RetValue = round((Value_cm * 0.3937008) * 100) / 100>
//	<cfelse>
//		<cfset variables.RetValue = round(Value_cm * 100) / 100>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfif EngMetric EQ 0>	
//			<cfset variables.RetValue = variables.RetValue & ' in'>
//		<cfelse>
//			<cfset variables.RetValue = variables.RetValue & ' cm'>
//		</cfif>
//	</cfif>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="kgORlbs" output="no" returntype="string">
//	<cfargument name="Value_Kg" type="string" required="yes">
//	<cfargument name="EngMetric" type="boolean" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Trim(Value_Kg) EQ "")>
//		<cfset variables.RetValue = "">
//	<cfelseif EngMetric EQ 0>	
//		<cfset variables.RetValue = round((Value_Kg * 2.2) * 100) / 100>
//	<cfelse>
//		<cfset variables.RetValue = round(Value_Kg * 100) / 100>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfif EngMetric EQ 0>
//			<cfset Variables.WeightDisplay = variables.RetValue \ 1 />
//			<cfset Variables.WeightOunces = round((variables.RetValue - (variables.RetValue \ 1)) * 16) />
//
//			<cfset variables.RetValue = Variables.WeightDisplay & ' lb'>
//			<cfif Variables.WeightOunces NEQ 0>
//				<cfset variables.RetValue = variables.RetValue & ' ' & Variables.WeightOunces & ' oz'>
//			</cfif>
//		<cfelse>
//			<cfset variables.RetValue = variables.RetValue & ' Kg'>
//		</cfif>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayBMI" output="no" returntype="string">
//	<cfargument name="Height_cm" type="string" required="yes">
//	<cfargument name="Weight_Kg" type="string" required="yes">
//	<cfargument name="ReturnForZero" type="string" required="no" default="">
//
//	<cfif ((Height_cm NEQ "0") AND (Height_cm NEQ "")) And ((Weight_Kg NEQ "0") AND (Weight_Kg NEQ ""))> 
//		<cfset Variables.RetValue = Round((Weight_Kg/((Height_cm/100)*(Height_cm/100)))*100)/100>
//	<cfelse>
//		<cfset Variables.RetValue = ReturnForZero>
//	</cfif>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplaySystolic" output="no" returntype="string">
//	<cfargument name="Systolic" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Systolic NEQ "")> 
//		<cfset Variables.RetValue = STZ(Systolic)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' mmhg'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayDiastolic" output="no" returntype="string">
//	<cfargument name="Diastolic" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Diastolic NEQ "")> 
//		<cfset Variables.RetValue = STZ(Diastolic)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' mmhg'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayBP" output="no" returntype="string">
//	<cfargument name="Systolic" type="string" required="yes">
//	<cfargument name="Diastolic" type="string" required="yes">
//
//	<cfif ((Systolic NEQ "0") And (Systolic NEQ "")) And ((Diastolic NEQ "0") And (Diastolic NEQ ""))> 
//		<cfset Variables.RetValue = STZ(Systolic) & '/' & STZ(Diastolic)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayRespiration" output="no" returntype="string">
//	<cfargument name="Respiration" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Respiration NEQ "")> 
//		<cfset Variables.RetValue = STZ(Respiration)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' breaths/min'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayPulse" output="no" returntype="string">
//	<cfargument name="Pulse" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (Pulse NEQ "")> 
//		<cfset Variables.RetValue = STZ(Pulse)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' beats/min'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="cecORfahr" output="no" returntype="string">
//	<cfargument name="Value_C" type="String" required="yes">
//	<cfargument name="EngMetric" type="boolean" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif Trim(Value_C) EQ "">
//		<cfset variables.RetValue = "">
//	<cfelseif EngMetric EQ 0>	
//		<cfset variables.RetValue = round(((Value_C * (9/5)) + 32) * 10) / 10>
//	<cfelse>
//		<cfset variables.RetValue = round(Value_C * 10) / 10>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfif EngMetric EQ 0>	
//			<cfset variables.RetValue = variables.RetValue & ' &deg;F'>
//		<cfelse>
//			<cfset variables.RetValue = variables.RetValue & ' &deg;C'>
//		</cfif>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayO2Sat" output="no" returntype="string">
//	<cfargument name="O2Saturation" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (O2Saturation NEQ "")> 
//		<cfset Variables.RetValue = STZ(O2Saturation)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' %'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DisplayHeadCirc" output="no" returntype="string">
//	<cfargument name="HeadCircumference" type="string" required="yes">
//	<cfargument name="DisplayUnits" type="boolean" required="no" default="0">
//
//	<cfif (HeadCircumference NEQ "")> 
//		<cfset Variables.RetValue = STZ(HeadCircumference)>
//	<cfelse>
//		<cfset Variables.RetValue = ''>
//	</cfif>
//
//	<cfif (DisplayUnits EQ 1) AND (variables.RetValue NEQ "")>
//		<cfset variables.RetValue = variables.RetValue & ' cm'>
//	</cfif>
//	
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="STZ" output="no" returntype="string">
//	<cfargument name="DecValue" type="numeric" required="yes">
//
//	<cfset variables.RetValue = DecValue / 1>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DefaultZero" output="no" returntype="string">
//	<cfargument name="CheckValue" type="string" required="yes">
//
//	<cfif (trim(CheckValue) EQ '')> 
//		<cfset Variables.RetValue = "0">
//	<cfelse>
//		<cfset Variables.RetValue = CheckValue>
//	</cfif>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//<cffunction name="DefaultBlank" output="no" returntype="string">
//	<cfargument name="CheckValue" type="string" required="yes">
//
//	<cfif (trim(CheckValue) EQ '0')> 
//		<cfset Variables.RetValue = "">
//	<cfelse>
//		<cfset Variables.RetValue = CheckValue>
//	</cfif>
//
//	<cfreturn #variables.RetValue#>
//</cffunction>
//
//


?>
<?php
//<cfif GraphName EQ "Bar">
//		<cfchart format="flash" chartheight="300" chartwidth="880" showxgridlines="yes" showygridlines="yes" databackgroundcolor="##D3D3D3" showborder="no" fontbold="yes" fontitalic="no" font="Garamond" fontsize="12" labelformat="number" xaxistitle="#GraphData.ResultsTestName#" show3d="yes" rotated="no" sortxaxis="no" showlegend="no" showmarkers="no" backgroundcolor="##D3D3D3">
//		<cfset Variables.LoopCount=1>
//		<cfset Variables.ColorLoopCount=1>
//		<cfloop list="#Variables.Data#" index="idx">
//			<cfif IsNumeric(Variables.idx)>
//		        <cfchartseries type="bar" seriescolor="###ListGetAt(MultipleBarColors,Variables.ColorLoopCount)#" paintstyle="shade">
//				<cfchartdata item="#DateFormat(ListGetAt(Variables.LegendBySet,Variables.LoopCount,','),'mm/dd/yyyy')#" value="#Variables.idx#">
//				</cfchartseries>
//			</cfif>
//		<cfset Variables.LoopCount=Variables.LoopCount+1>
//		<cfset Variables.ColorLoopCount=Variables.ColorLoopCount+1>
//		<cfif Variables.ColorLoopCount EQ 15>
//			<cfset Variables.ColorLoopCount=1>
//		</cfif>
//		</cfloop>
//		</cfchart>
//<cfelse>
//	<cfchart format="flash" chartheight="300" chartwidth="880" showxgridlines="yes" showygridlines="yes" databackgroundcolor="##D3D3D3" showborder="no" fontbold="yes" fontitalic="no" font="Garamond" fontsize="12" labelformat="number" xaxistitle="#GraphData.ResultsTestName#" show3d="no" rotated="no" sortxaxis="no" showlegend="no" showmarkers="yes" backgroundcolor="##D3D3D3">
//	<cfset Variables.LoopCount=1>
//	<cfchartseries type="line" seriescolor="##FF7F50" paintstyle="shade" markerstyle="diamond">
//	<cfloop list="#Variables.Data#" index="idx">
//		<cfif IsNumeric(Variables.idx)>
//       		<cfchartdata item="#DateFormat(ListGetAt(Variables.LegendBySet,Variables.LoopCount,','),'mm/dd/yyyy')#" value="#Variables.idx#">
//		</cfif>
//		<cfset Variables.LoopCount=Variables.LoopCount+1>				
//	</cfloop>		
//	</cfchartseries>
//	</cfchart>		
//</cfif>
?>
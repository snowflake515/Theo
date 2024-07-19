<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";?>
<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:hl7-org:v3" xsi:schemaLocation="urn:hl7-org:v3 CDA.xsd">
  <realmCode code="US"/>
  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040" />
  <templateId root='2.16.840.1.113883.10.20.22.1.10'/>
  <id extension='<?php echo $dt->Encounter_ID?>' root='2.16.840.1.113883.19'/>
  <code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="52040-3" displayName="Provider Report" />
  <title>Provider Report</title>
  <effectiveTime value="<?php echo  !empty($dt->EncounterDate) ? date('YmdHis', strtotime($dt->EncounterDate)) : '' ?>" />
  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25" />
  <languageCode code="en-US" />
  <recordTarget>
    <patientRole>
      <id root='2.16.840.1.113883.3.933' extension='<?php echo !empty($patient->MedicalRecordNumber) ? $patient->MedicalRecordNumber : ''?>'/>
      <addr>
        <streetAddressLine><?php echo !empty($patient->Addr1) ? $patient->Addr1 : ''?></streetAddressLine>
        <city><?php echo !empty($patient->City) ? $patient->City : ''?></city>
        <state><?php echo !empty($patient->State) ? $patient->State : ''?></state>
        <postalCode><?php echo !empty($patient->Zip) ? $patient->Zip : ''?></postalCode>
        <country></country>
      </addr>
      <telecom value='<?php echo !empty($patient->PhoneHome) ? 'tel:'.$patient->PhoneHome : ''?>'/>
      <patient>
        <name>
          <given><?php echo !empty($patient->FirstName) ? $patient->FirstName : ''?></given>
          <family><?php echo !empty($patient->LastName) ? $patient->LastName : ''?></family>
        </name>
        <administrativeGenderCode code="<?php echo !empty($patient->Sex) ? $patient->Sex : ''?>" codeSystem="2.16.840.1.113883.5.1"/>
        <birthTime value="<?php echo !empty($patient->DOB) ? date('Ymd', strtotime($patient->DOB)) : ''?>"/>
        <guardian>
          <id extension="23456" root="2.16.840.1.113883.19.5"/>
          <addr>
            <streetAddressLine>
            </streetAddressLine>
            <city></city>
            <state></state>
            <postalCode></postalCode>
            <country></country>
          </addr>
          <telecom value=""
          use="HP"/>
          <guardianPerson>
            <name>
              <given></given>
              <family></family>
            </name>
          </guardianPerson>
        </guardian>
      </patient>
    </patientRole>
  </recordTarget>
  <author>
    <time value="<?php echo date('YmdHis') ?>" />
    <assignedAuthor>
      <id root="2.16.840.1.113883.19.5" extension="Prevent1" />
      <addr>
        <streetAddressLine></streetAddressLine>
        <city></city>
        <state></state>
        <postalCode></postalCode>
        <country></country>
      </addr>
      <telecom value="tel:(800)890-1297" />
      <assignedAuthoringDevice>
        <manufacturerModelName>WelltrackOne</manufacturerModelName>
        <softwareName>PreventOne</softwareName>
      </assignedAuthoringDevice>
    </assignedAuthor>
  </author>
  <custodian>
    <assignedCustodian>
      <representedCustodianOrganization>
        <id extension='<?php echo !empty($org->Org_ID) ? $org->Org_ID : ''?>' root='1.3.6.4.1.4.1.2835.3'/>
        <name><?php echo !empty($org->OrgName) ? $org->OrgName : ''?></name>
        <telecom value='<?php echo !empty($org->OrgContact) ? 'tel:'.$org->OrgContact : ''?>' use='WP'/>
        <addr>
          <streetAddressLine><?php echo !empty($org->OrgAddr1) ? $org->OrgAddr1 : ''?></streetAddressLine>
          <city><?php echo !empty($org->OrgCity) ? $org->OrgCity : ''?></city>
          <state><?php echo !empty($org->OrgState) ? $org->OrgState : ''?></state>
          <postalCode><?php echo !empty($org->OrgZip) ? $org->OrgZip : ''?></postalCode>
          <country></country>
        </addr>
      </representedCustodianOrganization>
    </assignedCustodian>
  </custodian>
  <component>
    <nonXMLBody>
      <text mediaType="application/pdf" representation="B64"><?php echo !empty($base64) ? $base64 : ''?></text>
    </nonXMLBody>
  </component>
</ClinicalDocument>

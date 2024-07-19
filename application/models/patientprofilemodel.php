<?php

class PatientProfileModel extends CI_Model {

  var $table = "PatientProfile";
  var $key = "Patient_ID";

  function __construct() {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
    $this->data_db->trans_start();
  }

  function insert($data) {
    $this->data_db->trans_begin();
    $this->data_db->insert($this->table, $data);
    $this->data_db->trans_commit();
  }

  function update($id, $data) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->update($this->table, $data);
    $this->data_db->trans_commit();
  }

  function delete($id) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }

  function get_all() {
    return $this->data_db->get($this->table);
  }

  function get_by_id($id) {
    $this->data_db->where($this->key, $id);
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_by_field($field, $val, $other_condition = NULL) {
    $this->data_db->where($field, $val);
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_simple_limit($where = array(), $limit = 0, $ofsset = 0) {
    $this->data_db->limit($limit, $ofsset);
    $this->data_db->order_by('FirstName', 'desc');
    $this->data_db->from($this->table);
    foreach ($where as $key => $value) {
      $this->data_db->where($key, $value);
    }
    return $this->data_db->get();
  }

  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

  function get_count($data_search = NULL, $org_id = NULL) {

    $condition = $this->search_patient($data_search);
    $def = NULL;
    if (!$data_search) {
      $def = " AND (pap.Hidden = 0 OR pap.Hidden IS NULL) ";
    }
    $sql = "
                SELECT
                        COUNT(pap.Patient_ID) count_data
                FROM
                        PatientProfile as pap
                WHERE
                        pap.Org_ID = '$org_id'
                        $def
                        $condition
	 ";
    $query = $this->data_db->query($sql);
    if ($query) {
      return $query->row();
    }
  }

  function get_patients($page = 1, $data_search = NULL, $org_id = NULL, $count_data = 10) {

    $condition = $this->search_patient($data_search);
    $def = NULL;
    if (!$data_search) {
      $def = " AND (pap.Hidden = 0 OR pap.Hidden IS NULL) ";
    }
    $sql = "
               select top ($count_data) * from (
                    SELECT
                            pap.Patient_ID,
                            pap.LastName,
                            pap.FirstName,
                            pap.AccountNumber,
                            pap.MiddleName,
                            pap.SSN,
                            pap.DOB,
                            pap.Hidden,
                            pap.MedicalRecordNumber,
                            pap.Provider_ID,
                            prp.ProviderFirstName,
                            prp.ProviderMiddleName,
                            prp.ProviderLastName,
                            ROW_NUMBER() OVER (ORDER BY LastName asc, FirstName asc) AS Result_Number
                    FROM
                            PatientProfile AS pap
                    LEFT JOIN
                            ProviderProfile as prp
                    ON
                            pap.Provider_ID = prp.Provider_ID
                    WHERE
                            pap.Org_ID = '$org_id'
                            $def
                            $condition
            ) innerSel WHERE Result_Number > (($page - 1) * $count_data)";

    $query = $this->data_db->query($sql);

    if ($query) {
      return $query->result();
    }
  }

  function search_patient($data_search) {
    $q = "";
    if (is_array($data_search)) {
      foreach ($data_search as $key => $value) {
        if ($key == "Hidden") {
          if ($value == "ACTIVE") {
            $q .=" AND (pap.Hidden = 0 OR pap.Hidden IS NULL) ";
          } else if ($value == "NON_ACTIVE") {
            $q .=" AND pap.Hidden = 1 ";
          }
        } else {
          if ($value != "") {
            if ($key == "text_field") {
              $q .= " AND pap." . $data_search['by_field'] . " like '" . $data_search['text_field'] . "%'";
            } else if ($key == "dob") {
              $value = date('Y-m-d', strtotime($value));
              $q .= " AND pap.DOB = '" . $value . "'";
            } else if ($key != "text_field" and $key != "by_field") {
              $key = preg_replace("/[^A-Za-z,_]/", "", $key);
              if ($key == 'firstName' || $key == 'lastName') {
                $q .= " AND pap." . $key . " like '$value%'";
              } else {
                $q .= " AND pap." . $key . " = '$value'";
              }
            }
          }
        }
      }
    }
    return $q;
  }

  function validation_create($form_patient_name) {
    $config = array();
    if ($form_patient_name == "demographics") {
      $config = array(
          array('field' => 'FirstName', 'label' => 'Patient', 'rules' => 'required'),
          array('field' => 'LastName', 'label' => 'Patient', 'rules' => 'required'),
          array('field' => 'Provider_ID', 'label' => 'Provider', 'rules' => 'required'),
          array('field' => 'DOB', 'label' => 'DOB', 'rules' => 'required'),
      );
    } else if ($form_patient_name == "responsible_party") {
      $config = array(
          array('field' => 'RPFirstName', 'label' => 'RPFirstName', 'rules' => ''),
      );
    } else if ($form_patient_name == "office_information") {
      $config = array(
          array('field' => 'AccountNumber', 'label' => 'AccountNumber', 'rules' => ''),
          array('field' => 'Provider_ID', 'label' => 'Provider', 'rules' => 'required'),
      );
    }

//    $config = array(
//        array('field' => 'FirstName', 'label' => 'Patient', 'rules' => 'required'),
//        array('field' => 'MiddleName', 'label' => 'MiddleName', 'rules' => 'required'),
//        array('field' => 'LastName', 'label' => 'LastName', 'rules' => 'required'),
//        array('field' => 'NickName', 'label' => 'NickName', 'rules' => 'required'),
//        array('field' => 'Suffix', 'label' => 'Suffix', 'rules' => 'required'),
//        array('field' => 'DOB', 'label' => 'DOB', 'rules' => 'required'),
//        array('field' => 'Sex', 'label' => 'Sex', 'rules' => 'required'),
//        array('field' => 'MedicalRecordNumber', 'label' => 'MedicalRecordNumber', 'rules' => 'required'),
//        array('field' => 'MaritalStatus', 'label' => 'Sex', 'rules' => 'required'),
//        array('field' => 'BloodType', 'label' => 'Sex', 'rules' => 'required'),
//        array('field' => 'Race_EthnicityMaster_ID', 'label' => 'Sex', 'rules' => 'required'),
//        array('field' => 'Ethnicity_EthnicityMaster_ID', 'label' => 'Ethnicity_EthnicityMaster_ID', 'rules' => 'required'),
//        array('field' => 'PreferredContact', 'label' => 'PreferredContact', 'rules' => 'required'),
//        array('field' => 'AddressType', 'label' => 'AddressType', 'rules' => 'required'),
//        array('field' => 'Addr1', 'label' => 'Addr1', 'rules' => 'required'),
//        array('field' => 'Addr2', 'label' => 'Addr2', 'rules' => 'required'),
//        array('field' => 'State', 'label' => 'State', 'rules' => 'required'),
//        array('field' => 'Zip', 'label' => 'Zip', 'rules' => 'required'),
//        array('field' => 'PhoneHome', 'label' => 'PhoneWork', 'rules' => 'required'),
//        array('field' => 'Email', 'label' => 'Email', 'rules' => 'required'),
//        array('field' => 'PrimaryIns', 'label' => 'PrimaryIns', 'rules' => 'required'),
//        array('field' => 'PrimaryInsPol', 'label' => 'PrimaryInsPol', 'rules' => 'required'),
//        array('field' => 'PrimaryInsEffectiveDate', 'label' => 'PrimaryInsEffectiveDate', 'rules' => 'required'),
//        array('field' => 'SecondaryIns', 'label' => 'SecondaryIns', 'rules' => 'required'),
//        array('field' => 'SecondaryInsPol', 'label' => 'SecondaryInsPol', 'rules' => 'required'),
//        array('field' => 'SecondaryInsEffectiveDate', 'label' => 'SecondaryInsEffectiveDate', 'rules' => 'required'),
//        array('field' => 'Notes', 'label' => 'Notes', 'rules' => 'required'),
//        array('field' => 'LanguageMaster_ID', 'label' => 'Notes', 'rules' => 'required'),
//        array('field' => 'SSN', 'label' => 'SSN', 'rules' => 'required')
//    );
    return $config;
  }

  function validation_update($form_patient_name) {
    return $this->validation_create($form_patient_name);
  }

  function get_params_demographics() {
    $PrimaryInsEffectiveDate = ($this->input->post('PrimaryInsEffectiveDate') != "") ? $this->input->post('PrimaryInsEffectiveDate') : NULL;
    $SecondaryInsEffectiveDate = ($this->input->post('SecondaryInsEffectiveDate') != "") ? $this->input->post('SecondaryInsEffectiveDate') : NULL;

    $post = array(
        'FirstName' => $this->input->post('FirstName'),
        'MiddleName' => $this->input->post('MiddleName'),
        'LastName' => $this->input->post('LastName'),
        'NickName' => $this->input->post('NickName'),
        'Suffix' => $this->input->post('Suffix'),
        'DOB' => $this->input->post('DOB'),
        'Sex' => $this->input->post('Sex'),
        'SSN' => $this->input->post('SSN'),
        'MedicalRecordNumber' => $this->input->post('MedicalRecordNumber'),
        'LanguageMaster_ID' => $this->input->post('LanguageMaster_ID'),
        'MaritalStatus' => $this->input->post('MaritalStatus'),
        'BloodType' => $this->input->post('BloodType'),
        'Race_EthnicityMaster_ID' => $this->input->post('Race_EthnicityMaster_ID'),
        'Ethnicity_EthnicityMaster_ID' => $this->input->post('Ethnicity_EthnicityMaster_ID'),
        'PreferredContact' => $this->input->post('PreferredContact'),
        'Notes' => $this->input->post('Notes'),
        'AddressType' => $this->input->post('AddressType'),
        'Addr1' => $this->input->post('Addr1'),
        'Addr2' => $this->input->post('Addr2'),
        'City' => $this->input->post('City'),
        'State' => $this->input->post('State'),
        'Zip' => $this->input->post('Zip'),
        'PhoneHome' => $this->input->post('PhoneHome'),
        'PhoneCell' => $this->input->post('PhoneCell'),
        'Email' => $this->input->post('Email'),
        'EmerContact' => $this->input->post('EmerContact'),
        'Provider_ID' => $this->input->post('Provider_ID'),
        'PrimaryIns' => $this->input->post('PrimaryIns'),
        'PrimaryInsPol' => $this->input->post('PrimaryInsPol'),
        'PrimaryInsEffectiveDate' => $PrimaryInsEffectiveDate,
        'SecondaryIns' => $this->input->post('SecondaryIns'),
        'SecondaryInsPol' => $this->input->post('SecondaryInsPol'),
        'SecondaryInsEffectiveDate' => $SecondaryInsEffectiveDate,
    );

    return $post;
  }

  function get_params_office() {

    $post = array(
        'AccountNumber' => $this->input->post('AccountNumber'),
        'Participant_Id' => $this->input->post('Participant_Id'),
        'Provider_ID' => $this->input->post('Provider_ID'),
        'AlertNotes' => $this->input->post('AlertNotes'),
        'Hidden' => ($this->input->post('Hidden') == 1) ? 1 : 0,
        'Confidential' => ($this->input->post('Confidential') == 1),
        'DOD' => ($this->input->post('DOD') != "") ? $this->c_date($this->input->post('DOD')) : NULL,
    );
    return $post;
  }

  function get_params_responsible_party() {
    $post = array(
        'Relationship_ID' => $this->input->post('Relationship_ID'),
        'RPFirstName' => $this->input->post('RPFirstName'),
        'RPMiddleName' => $this->input->post('RPMiddleName'),
        'RPLastName' => $this->input->post('RPLastName'),
        'RPSSN' => $this->input->post('RPSSN'),
        'RPAddr1' => $this->input->post('RPAddr1'),
        'RPAddr2' => $this->input->post('RPAddr2'),
        'RPCity' => $this->input->post('RPCity'),
        'RPState' => $this->input->post('RPState'),
        'RPPhoneHome' => $this->input->post('RPPhoneHome'),
        'RPPhoneWork' => $this->input->post('RPPhoneWork'),
        'RPPhoneCell' => $this->input->post('RPPhoneCell'),
        'RPEmail' => $this->input->post('RPEmail'),
    );
    return $post;
  }

  function rules_valid_demographics() {
    $config = array(
        array('field' => 'FirstName', 'label' => 'Patient', 'rules' => 'required'),
        array('field' => 'LastName', 'label' => 'Patient', 'rules' => 'required'),
        array('field' => 'Provider_ID', 'label' => 'Provider', 'rules' => 'required'),
        array('field' => 'DOB', 'label' => 'DOB', 'rules' => 'required'),
    );
    return $config;
  }

  function rules_valid_office() {
    $config = array(
        array('field' => 'Provider_ID', 'label' => 'Provider', 'rules' => 'required'),
    );
    return $config;
  }

  function rules_valid_responsible_party() {
    $config = array(
        array('field' => 'Relationship_ID', 'label' => 'Relationship', 'rules' => ''),
        array('field' => 'RPEmail', 'label' => 'Email', 'rules' => 'valid_email'),
    );
    return $config;
  }

  private function c_date($date = NUll) {
    return date('Y-m-d H:i', (strtotime($date)));
  }

}

<?php
    if ($Field === 'CLINICAL') {
        $this->load->view('encounter/printClinical');
    }elseif($Field === 'PATIENT'){
        $this->load->view('encounter/printPatient');
    } else{
        $this->load->view('encounter/printchartnotes');
    }
?>
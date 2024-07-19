<?php
    if ($Field === 'CLINICAL') {
        $this->load->view('encounter/printClinical');
    }elseif($Field === 'PATIENT'){
        $this->load->view('encounter/printPatient');
    }elseif($Field === 'AICAREPLAN'){
        $this->load->view('encounter/printAICarePlan');
    }else{
        $this->load->view('encounter/printchartnotes');
    }
?>
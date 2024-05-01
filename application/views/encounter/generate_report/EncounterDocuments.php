<?php
    if ($Field === 'CLINICAL') {
        $this->load->view('encounter/printClinical');
    }else{
        $this->load->view('encounter/printchartnotes');
    }
?>

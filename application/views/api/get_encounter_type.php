<?php

$blank = ($encounter->num_rows() == 1) ? TRUE : FALSE;
$option = option_select($encounter->result(), 'EncounterDescription_ID', 'EncounterDescription', "[Select]", $blank);
echo form_dropdown('EncounterDescription_ID', $option, $this->input->post('select'), 'class = "form-control" id="EncounterDescription_ID"');
echo form_error('EncounterDescription_ID');
?>  
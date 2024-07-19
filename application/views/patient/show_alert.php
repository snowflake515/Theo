<?php
$is_valid = validation_errors();
if(!empty($is_valid)){
  echo '<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> Please enter required information.</div>';
}

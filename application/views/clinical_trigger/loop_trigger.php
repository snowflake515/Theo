<?php
$i = 0;
$temp_desc = NULL;
echo '<div class="ctshelper2">';
foreach ($clinical_trigger->result() as $dt) {
  if ($temp_desc != $dt->TTDescription) {
//    if ($i != 0) {
//     // echo '<div class="big-line" style="border: 1px solid #f2f2f2; height: 1px; margin-top: 20px;"></div>';
//    }
    echo '<p class="title_line" ><span>' . $dt->TTDescription . '</span></p>';
  }
  
  echo '<div class="ttdisplay" style="margin-left: 20px; margin-bottom: 10px">' . html_entity_decode($dt->TTDisplay). ' </div>';
  $temp_desc = $dt->TTDescription;
  $i++;
}
echo '</div>';
<?php

$con = '(Hidden = 0 OR Hidden IS NULL)';
if ($tml1) {
  $template2 = $this->Tml2Model->get_by_field('TML1_ID', $tml1, $con)->result();

  foreach ($template2 as $val) {
    $checked = ($val->PreSelected) ? 'checked' : NULL;

    echo '<div class="checkbox cstm-cb">  <label> <input type="checkbox" class="tml2_v" value="' . $val->TML2_ID . '"  ' . $checked . ' onclick="change_new_tml2(this)">  <span>  <i class="icon icon-check"></i>  <i class="icon icon-unchecked"></i> </span> ' . $val->TML2_Description . '  </label>  </div>';
  }
}

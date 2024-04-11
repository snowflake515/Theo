<div class="page-header">
  <h1>
    Appointment Print
  </h1>
</div>

<!-- Default panel contents -->
<div class="table-header">
  <b><?php echo date('F jS  Y', strtotime($this->input->post('start'))) . ' - ' . date('F jS  Y', strtotime($this->input->post('end'))) ?></b>
</div>


<table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
         <th>Time</th>
        <th>Patient</th>
        <th>Provider</th>
        <th>Status</th>
        <th>DOB</th>
        <th>Phone</th>
        <th>Account Number</th>
        
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 0;

      foreach ($listing as $val) {
        $i++;
        
        $provider = $this->ProviderProfileModel->get_by_id($val->Provider_ID)->row();
        $get_last_check = $this->CheckInLogModel->get_last_check($val->Appointments_ID)->row();
        $s = ($get_last_check) ? $get_last_check->CodeOrder : 0;
        $checkIn_codes = $this->CheckInCodeModel->get_by_field('CodeOrder', $s , array('Org_Id' => $this->current_user->Org_Id))->row();
        $color = ($checkIn_codes) ? $checkIn_codes->Color : NULL;
        $width = ($checkIn_codes) ? '40%' : 0;
        $phone = ($val->PhoneHome) ? $val->PhoneHome : $val->PhoneWork;
        $phone = (!empty($phone)) ?  sprintf('%s', $phone): NULL;
        $acc_num = sprintf('%s', $val->AccountNumber);
        $cs = (!empty($checkIn_codes->Description)) ? $checkIn_codes->Description: NULL;

          echo '<tr>'
          . '<td>'.$i.'</td>'
          . '<td>'. date('m/d/Y h:i A', strtotime($val->ApptStart)) . "<br/>" . date('m/d/Y H:i A', strtotime($val->ApptStop)) . "</td>"
          . '<td > ' . $val->LastName . ', ' . $val->FirstName . '</td>'
          . '<td ><strong> </strong>' . $provider->ProviderLastName . ', ' . $provider->ProviderFirstName . '</td>'
          . '<td ><strong> </strong>' . $cs . ''. "" . '</td>'
          . '<td >' . "" . '' . date("m/d/Y", strtotime($val->DOB)) . '</td>'
          . '<td >' . $phone . '</td>'
          . '<td >' . $acc_num . '</td>'
          . '</tr>';
      }
      ?>

    </tbody>
  </table>


<div class="clearfix">
  <br/>
</div>

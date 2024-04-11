<div class="page-header">
  <h1>
    Patient Appointment History
  </h1>
</div>


<?php echo anchor('schedule/appointment_new/' . $patient->Patient_ID . '?&provider=' . $patient->Provider_ID, '<i class="icon icon-book"></i>  &nbsp; &nbsp; Add appointment', array('class' => 'btn btn-primary')); ?>
<div class="clearfix"> <br/></div>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th width="50">#</th>
        <th>Date</th>
        <th>Time</th>
        <th>Reason</th>
        <th>Provider</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $n = 1;
      $status = NULL;
      foreach ($dt as $lt) {

        $status = $this->CheckInLogModel->get_last_check($lt->Appointments_ID)->row();
        $cek_s = ($status) ? $status->CodeOrder : 0;
        $get_status = $this->CheckInCodeModel->get_by_field('CodeOrder', $cek_s, array('Org_Id' => $this->current_user->Org_Id))->row();
        $status = ($get_status) ? $get_status->Description : NULL;
        if ($status):
          echo "<tr>";
          echo "<td>$n</td>";
          echo "<td>" . anchor('schedule/appointment_edit/' . $lt->Appointments_ID, date("d M Y", strtotime($lt->ApptStart))) . "</td>";
          echo "<td>" . date("H:i ", strtotime($lt->ApptStart)) . "</td>";
          echo "<td>" . $lt->Notes . "</td>";
          echo "<td>" . $lt->ProviderFirstName . " " . $lt->ProviderLastName . "</td>";
          echo "<td>$status  </td>";
          echo "</tr>";
          $n++;
        endif;
      }
      ?>

    </tbody>
  </table>
</div>
<?php echo anchor('patient', '<i class="icon icon-arrow-left"></i> Back', array('class' => 'btn btn-default')); ?>
   


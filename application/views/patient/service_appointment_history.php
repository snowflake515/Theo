<div class="modal-body">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
 <h4> Patient Appointment History</h4>
 
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th width="30">#</th>
        <th width="100">Date</th>
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
          echo "<td style='word-break: break-word;'>" . $lt->Notes . "</td>";
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
 
   


</div> 

<div class="page-header">
  <h1>
    Patient Encounters History
  </h1>
</div>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th width="50">#</th>
        <th>Date</th>
        <th>Provider</th>
        <th>Report Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $n = 0;
      foreach ($encounter_history as $val) {
        $n++;
        echo "<tr>";
        echo "<td>$n</td>";
        echo "<td>" . anchor('encounter/start/' . $val->Appointments_ID, date("d M Y", strtotime($val->EncounterDate))) . "</td>";
        echo "<td>" . $val->ProviderFirstName . " " . $val->ProviderMiddleName . " " . $val->ProviderLastName . "</td>";
        if ($val->AWACSStatus == 99) {
          $status = "Complete";
        } else {
          $status = "Pending";
        }
        echo "<td>" . $status . "</td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<?php echo anchor('patient', '<i class="icon icon-arrow-left"></i> Back', array('class' => 'btn btn-default')); ?>
 


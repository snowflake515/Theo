<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
      <h4 class="modal-title">Get Patients</h4>
    </div>
    <div class="modal-body">

      <div class="table-responsive">

        <table class="table table-striped" width="100%">
          <thead> 
            <tr>
              <th>#</th>
              <th>Patient Name</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($patients as $dt) {
              echo "<tr>";
              echo "<td><input type='radio' name='Patient_ID' class='pick_patien' value='".$dt->Patient_ID."' data-name='".$dt->LastName.', '.$dt->FirstName."'></td>";
              echo "<td>$dt->LastName $dt->FirstName</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default noRadius" data-dismiss="modal">Close</button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $('.pick_patien').change(function(){
    select_patien($(this));
  })
</script>
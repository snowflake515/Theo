<form id="select_template_form" action="" method="post" accept-charset="utf-8" style="display: none;">        
  <input type="hidden" name="submit_form" value="patient_save">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">Verify Cell Phone and Email for "5Dec, Today "</h4>
  </div>
  <div class="modal-body">
    <p class="text-muted">Please verify the patient’s cell phone number and email address. If the patient asks for
      the reason for this information, inform them that we will only use it to send secure
      patient reports and clinical information to them via our ConnectONE<span style="font-size: 9px; position: relative; top: -3px">tm</span> portal.</p>
    <div class="form-group">
      <label class="control-label">Cell Phone Number :</label>
      <input type="text" class="form-control" name="PhoneHome" value="">
    </div>
    <div class="form-group" style="margin: 0">
      <label class="control-label">Email Address :</label>
      <input type="text" class="form-control" name="Email" value="">
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">Next</button>
  </div>
</form>
<!-- <script>
    window.onload = function() {
        let selectTemplateForm = document.getElementById("select_template_form");
        let currentURL = window.location.href;
        selectTemplateForm.action = currentURL;
    };
</script> -->
<?php
if ($this->mylib->dt_current_user()) {

  $exp = array('ORG1', 'AWACS1');
  if ($this->mylib->dt_current_user()->ResetPassword != 0 || $this->session->flashdata('pass_success_msg') != "") {
    ?>
    <!-- Modal -->
    <div class="modal fade in modal_bg modal-hidden-scroll" id="passModal" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="post" class="form-horizontal" action="<?php echo site_url('user/change_pass') ?>" accept-charset="UTF-8">
            <div class="modal-header">
              <h4 class="modal-title" >Change Password</h4>
            </div>
            <div class="modal-body">
              <?php if ($this->session->flashdata('pass_success_msg') == ""): ?>
                <p>Your System Administrator is requiring you to change your password. Please enter a NEW password in the fields below.<br>
                  <i class="text-muted note-text">Note: We recommend combination of letters and numbers in your new password.</i>
                </p>
                <div class="form-group">	
                  <label class="control-label col-sm-4" for="new-password">New password:</label>	
                  <div class="col-sm-8">		
                    <input type="password" class="form-control" name="new-password" placeholder="Enter new password">
                    <?php echo form_error('new-password'); ?>
                  </div>
                </div>
                <div class="form-group">	
                  <label class="control-label col-sm-4" for="confirm-password">Confirm password:</label>	
                  <div class="col-sm-8">		
                    <input type="password" class="form-control" name="confirm-password" placeholder="Confirm password">
                    <?php echo form_error('confirm-password'); ?>
                  </div>
                </div>
              <?php else: ?>
                <?php echo $this->session->flashdata('pass_success_msg'); ?>
              <?php endif; ?>
            </div>
            <div class="modal-footer">
              <?php if ($this->session->flashdata('pass_success_msg') == ""): ?>
                <button type="submit" class="btn btn-primary">Submit</button>
              <?php else: ?>
                <a href="<?php echo site_url('schedule')?>" class="btn btn-primary">Close</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
  } elseif (!$this->session->userdata('CHECK_ORG') && in_array($this->mylib->dt_current_user()->User_Id, $exp)) {
    ?>
    <!-- Modal -->
    <div class="modal fade in modal_bg modal-hidden-scroll" id="orgModal" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="post" class="form-horizontal" action="<?php echo site_url('user/change_org') ?>" accept-charset="UTF-8">
            <div class="modal-header">
              <h4 class="modal-title" >Select Organization</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label col-sm-2">Organization:</label>
                <div class="col-sm-10">
                  <?php
                  $get_orgs = $this->OrgProfileModel->get_all_org();
                  $option = option_select($get_orgs->result(), 'Org_ID', 'OrgName', "Please select your organization");
                  echo form_dropdown('Org_ID', $option, '', 'class = "form-control"');
                  echo form_error('Org_ID');
                  ?>
                </div> 
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
  }
}
?>
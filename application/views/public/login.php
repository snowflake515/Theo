

<h4 class="header blue lighter bigger">
  <i class="icon-coffee green"></i>
  Please Enter Your Information
</h4>
<div class="space-6"></div>
<?php echo form_open('session/login?c='.$this->input->get('c'))?>
  <?php echo form_error('userid'); ?>
  <fieldset>
    <label class="block clearfix">
      <span class="block input-icon input-icon-right">
        <label>Username</label>
        <input type="text" class="form-control"  name="userid"   placeholder="Enter Username"/>
        <i class="icon-user" style="top:35px"></i>
      </span>
    </label>
    <label class="block clearfix">
      <span class="block input-icon input-icon-right">
        <label>Password</label>
        <input type="password" class="form-control"  name="password"  placeholder="Enter Password" />
        <i class="icon-lock" style="top:35px"></i>
      </span>
    </label>
    <label class="block clearfix">
      <span class="block input-icon input-icon-right">
        <label>Client Number</label>
        <input type="text" class="form-control"   name="client_number"  placeholder="Enter Client Number" />
        <i class="icon-suitcase" style="top:35px"></i>
      </span>
    </label>
    <div class="space"></div>
    <div class="clearfix">
      <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
        <i class="icon-key" ></i>
        Login
      </button>
    </div>
    <div class="space-4"></div>
  </fieldset>
<?php echo form_close()?>

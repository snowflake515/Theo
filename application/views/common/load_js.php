
<script src="<?php echo base_url('assets/app/js/jquery-1.10.2.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/jquery-ui.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/jquery.ui.touch-punch.min.js') ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/fullcalendar.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/pace.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/prettyCheckable.min.js') ?>"></script>
<script>
  
<?php if ($this->agent->is_browser('Chrome') || $this->agent->is_browser('Safari') ) {
  ?> var support_dateinput = true;
<?php } else { ?>
    var support_dateinput = false;
<?php } ?>
  <?php if ($this->agent->is_mobile() ) {
  ?> var is_mobile = true;
<?php } else { ?>
    var is_mobile = false;
<?php } ?>
  var mysite = '<?php echo base_url(); ?>';
  var cuurent_date = '<?php echo $this->session->userdata('CURRENT_CALENDAR')?>';
</script>

<script src="<?php echo base_url('assets/app/js/app.js') ?>"></script>

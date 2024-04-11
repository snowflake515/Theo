<div class="page-header">
  <h1>
    Schedule 
  </h1>
</div>
<div class="widget-box">
  <div class="widget-header widget-header-blue widget-header-flat">
    <h4 class="lighter">Calendar </h4>
  </div>


  <div class="widget-body">
    <?php echo anchor('schedule/appointment_new', '<i class="icon icon-book"></i>  &nbsp; Add appointment', array('class' => 'btn btn-default')); ?>

    <div class="widget-main">

      <div class="clearfix"><br/></div>
      <form method="get" action="<?php echo site_url('appointment/get_day') ?>" id='form_appt_get' accept-charset="UTF-8">
        <input type="hidden" id='current_select' name="current_select">
        <input type="hidden" id='current_time' name="current_time">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">Provider</div>
                <div class="col-sm-8"><?php
                  $result = array();
                  $result[''] = '[All Providers]';
                  foreach ($provider as $p) {
                    $result[$p->Provider_ID] = $p->ProviderLastName.', '.$p->ProviderFirstName;
                  }
                  $option =  $result;
                  echo form_dropdown('Provider_ID', $option, form_value('Provider_ID', NULL), 'class = "form-control" id="select_provider_calendar"');
                  ?></div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">Jump to Date</div>
                <div class="col-sm-8">
                  <?php
                  $current_calendar = date('m-d-Y');
                  if ($this->session->userdata('CURRENT_CALENDAR') != "") {
                    $tmp = explode('-', $this->session->userdata('CURRENT_CALENDAR'));
                    $current_calendar = $tmp[1] . '-' . $tmp[2] . '-' . $tmp[0];
                  }
                  ?>

                  <input type="text" name="change_date" class="form-control datepicker" readonly="" id="change_date" value="<?php echo $current_calendar ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">View</div>
                <div class="col-sm-8">
                  <select class="form-control" name="view_calendar" id='view_calendar'>
                    <option value="month">Month</option>
                    <option value="basicWeek">Weeks</option>
                    <option value="agendaDay" selected="selected">Days</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div><!-- /widget-main -->
  </div><!-- /widget-body -->
</div>

<div class="clearfix">
  <br/>
  <div class="calendar_container">
    <div id="alert-calendar" class="alert alert-danger">You need to reload the calendar,  <a href="#" class="pull-right" onclick="javascript:refesh_calendar();
        return false;">Click here!</a></div>
    <div id='calendar'></div>  
  </div>
</div>

<p>&nbsp;</p>

<form target="_blank" method="post" action="<?php echo site_url('schedule/print_cal')?>" id="print_current_call" accept-charset="UTF-8">
  <input type="hidden" name="provider" id="xr_provider"/>
  <input type="hidden" name="view" id="xr_view"/>
  <input type="hidden" name="start" id="xr_start"/>
  <input type="hidden" name="end" id="xr_end"/>
  <button type="submit" class="btn btn-primary" id="print_calendar">Print Schedule</button>
</form>

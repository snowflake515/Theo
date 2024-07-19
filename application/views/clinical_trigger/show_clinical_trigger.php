<style>
  @media print
  {
    .navbar, .sidebar, .hidden-print{
      display: none;
    }
    .enable-print{
       display: block;
    }
    .main-content{
      margin:0;
    }
    .page-header h1{
      text-align: left;
    }
  }
</style>

<div id="render_me">
  <div class="page-header">
  <h1>Clinical Triggers</h1>
</div>


<div class="widget-box enable-print" >
  <?php if ($patient) { ?>
    <div class="widget-header widget-header-blue ">
      <h4 class="lighter">Clinical Triggers for "<?php echo $patient->LastName . ', ' . $patient->FirstName ?>"</h4>
    </div>
    <div class="widget-body">
      <div class="widget-main">
        <?php
        $links = NULL;
        if ($err) {
          echo $err;
        } else {
          if ($clinical_trigger && $clinical_trigger->num_rows() > 0) {
           $t_d['clinical_trigger'] = $clinical_trigger;
           echo $this->load->view('clinical_trigger/loop_trigger', $t_d, TRUE);
           echo '<div class="big-line"></div>';
            $links .= '<br/><a href="' . site_url('encounter/start/' . $ecounter->Appointments_ID) . '" class="hidden-print btn btn-default"><i class="icon icon-angle-left"></i>&nbsp;  Back</a>&nbsp;';
            $links .= '<a href="'.  site_url('clinical_trigger/encounter/' . $ecounter->Encounter_ID).'/print" class="btn btn-primary hidden-print"  data-name="clinical-trigger" target="_blank"><i class="icon icon-print"></i>&nbsp;  Print</a>';
          }else{
            echo 'There are no recommended treatment options for this patient';
            echo '<div class="big-line"></div>';
            $links .= '<br/><a href="' . site_url('encounter/start/' . $ecounter->Appointments_ID) . '" class="hidden-print btn btn-default"><i class="icon icon-angle-left"></i>&nbsp;  Back</a>&nbsp;';
          }
          $links .= '&nbsp;<a href="https://welltrackone.co/citations" class="hidden-print btn btn-success" target="_blank">  Citations</a>';
        }
        ?>

      </div>
    </div>
  <?php } else {

  } ?>
</div>
</div>

<?php echo $links;?>

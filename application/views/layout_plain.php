<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>WellTrackONE</title>
    <link href="<?php echo base_url('assets/ace/css/') ?>/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace.min.css" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/ace/img/faicon.png') ?>">
    <style>
      body{
        background: #fff;
      }
      .print-area_dv{
        font-size: 12px;
        max-width: 960px;
        margin: 0 auto; 
        padding: 20px;
      }

     
    </style>
  </head>
  <body>
    <div class="print-area_dv">
      <?php $this->load->view($partial); ?>
    </div>

    <script src='<?php echo base_url('assets/ace/js/') ?>/jquery-1.10.2.min.js'></script>
    
  </body>
</html>

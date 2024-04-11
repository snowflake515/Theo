<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <?php
    $c = set_value('c', $this->input->get('c'));
    $lt = $this->ClientImagesModel->get_by_field('Hidden', 0, array('ClientAccess' => $c))->row();
    $title_client = 'WellTrackONE';
    if(!empty($c) && $lt){
        $title_client = $lt->ClientName;
    }
    ?>
    <title><?php echo $title_client;?> - Welcome</title>
    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- basic styles -->
    <link href="<?php echo base_url('assets/ace/css/') ?>/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/font-awesome.min.css" />
    <!--[if IE 7]>
      <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/font-awesome-ie7.min.css" />
    <![endif]-->
    <!-- page specific plugin styles -->
    <!-- fonts -->
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-fonts.css" />
    <!-- ace styles -->
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace.min.css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-rtl.min.css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/additional.css?<?php echo date('YmdHis') ?>" />
    <!--[if lte IE 8]>
      <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-ie.min.css" />
    <![endif]-->
    <!-- inline styles related to this page -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url('assets/ace/js/') ?>/html5shiv.js"></script>
    <script src="<?php echo base_url('assets/ace/js/') ?>/respond.min.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="<?php echo base_url('assets/ace/img/faicon.png') ?>">
    <link rel="apple-touch-icon" sizes="129x129" href="<?php echo base_url('assets/ace/img/appleicon.png') ?>">
  </head>

  <body class="login-layout">
    <div class="main-container">
      <div class="main-content">
        <div class="row">
          <div class="col-sm-10 col-sm-offset-1">
            <div class="login-container">
              <br/>
              <div class="center">

                 <?php

                 if(!empty($c) && $lt && !empty($lt->ImageFile)){
                   echo '<img id="logo" style="max-width:300px; margin-top:10px; margin-bottom:10px" src="data:' . $lt->ImageType . ';base64,' . base64_encode($lt->ImageFile) . '" alt="My image alt" />';
                 }else{
                   echo '  <img src="'.base_url('assets/ace/img/logo.jpg').'" id="logo"/>';
                 }
                 ?>
                 <br>
              </div>

              <div class="space-6"></div>

              <div class="position-relative">
                <div id="login-box" class="login-box visible widget-box no-border">
                  <div class="widget-body">
                    <div class="widget-main">

                      <?php $this->load->view($partial); ?>
                    </div><!-- /widget-main -->
                  </div><!-- /widget-body -->
                </div><!-- /login-box -->
              </div><!-- /position-relative -->
            </div>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div>
    </div><!-- /.main-container -->
    <script src="<?php echo base_url('assets/app/js/jquery-1.10.2.min.js') ?>"></script>
    <!-- basic scripts -->

    <!--[if !IE]> -->

    <script type="text/javascript">
      window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>" + "<" + "/script>");
    </script>

    <!-- <![endif]-->

    <!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

    <script type="text/javascript">
      if ("ontouchend" in document)
        document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
    </script>

    <!-- inline scripts related to this page -->

    <script type="text/javascript">
      function show_box(id) {
        jQuery('.widget-box.visible').removeClass('visible');
        jQuery('#' + id).addClass('visible');
      }
    </script>
    <script>
      var mysite = '<?php echo base_url(); ?>index.php/';
      var cuurent_date = false, current_year = false, current_month = false, current_day = false;
      cuurent_date = '<?php echo $this->session->userdata('CURRENT_CALENDAR') ?>';
    </script>
    <script src="<?php echo base_url('assets/app/js/app.js?' . date('YmdHis')) ?>"></script>
  </body>
</html>

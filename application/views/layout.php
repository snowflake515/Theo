<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <?php
  $user = $this->mylib->dt_current_user();
  $title_client = 'WellTrackONE';
  if($user){
    $db = $this->data_db = $this->load->database('image', TRUE);
    $client = $db->from($this->ClientImagesModel->table)->select('ClientName, ClientAccess')
    ->where(array( 'Hidden' => 0, 'Org_ID' => $user->Org_Id )) ->get()->row();
    if($client){
      $title_client = $client->ClientName;
    }
  }

  ?>
  <title><?php echo $title_client?></title>

  <meta name="description" content="overview &amp; stats" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- basic styles -->

  <link href="<?php echo base_url('assets') ?>/css/bootstrap-pincode-input.css" rel="stylesheet">

  <script src="<?php echo base_url('assets') ?>/js/bootstrap-pincode-input.js"></script>

  <link href="<?php echo base_url('assets/ace/css/') ?>/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/font-awesome.min.css" />

  <!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/font-awesome-ie7.min.css" />
  <![endif]-->

  <!-- page specific plugin styles -->

  <!-- fonts -->
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/fullcalendar.css" />

  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/jquery-ui-1.10.3.full.min.css" />

  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-fonts.css" />

  <!-- ace styles -->

  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace.min.css" />
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-rtl.min.css" />
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-skins.min.css" />
   
  <link rel="stylesheet" href="<?php echo base_url('assets/app/css/prettyCheckable.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/additional.css?<?php echo date('YmdHis') ?>" />
  <!--[if lte IE 8]>
  <link rel="stylesheet" href="<?php echo base_url('assets/ace/css/') ?>/ace-ie.min.css" />
  <![endif]-->

  <!-- inline styles related to this page -->

  <!-- ace settings handler -->

  <script src="<?php echo base_url('assets/ace/js/') ?>/ace-extra.min.js"></script>

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

  <!--[if lt IE 9]>
  <script src="<?php echo base_url('assets/ace/js/') ?>/html5shiv.js"></script>
  <script src="<?php echo base_url('assets/ace/js/') ?>/respond.min.js"></script>
  <![endif]-->

  <link rel="shortcut icon" href="<?php echo base_url('assets/ace/img/faicon.png') ?>">

</head>

<body>
  <div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
    try {
      ace.settings.check('navbar', 'fixed')
    } catch (e) {
    }
    </script>

    <div class="navbar-container" id="navbar-container">
      <div class="navbar-header pull-left">
        <a href="#" class="navbar-brand">
          <small>
            <?php echo $title_client ?>
          </small>
        </a><!-- /.brand -->
      </div><!-- /.navbar-header -->

      <div class="navbar-header pull-right" role="navigation">
        <ul class="nav ace-nav">
          <li class="light-blue">
            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
              <span class="user-info">
                <small>Welcome,</small>
                <?php if ($this->mylib->dt_current_user()): ?>
                  <?php echo $this->mylib->dt_current_user()->Lname . ', ' . $this->mylib->dt_current_user()->Fname ?>
                <?php endif; ?>
              </span>
              <i class="icon-caret-down"></i>
            </a>
            <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
              <?php
              $exp = array('ORG1', 'AWACS1');
              if ($this->mylib->dt_current_user() && in_array($this->mylib->dt_current_user()->User_Id, $exp)) :
                ?>
                <li>
                  <a href="<?php echo site_url('user/rm_org') ?>">
                    <i class="icon-group"></i>
                    Change org
                  </a>
                </li>
                <?php
              endif;
              ?>
              <li>
                <?php $c = !empty($client->ClientAccess) ? "?c=$client->ClientAccess" : NULL ?>
                <a href="<?php echo site_url('session/logout'.$c) ?>">
                  <i class="icon-off"></i>
                  Logout
                </a>
              </li>
            </ul>
          </li>
        </ul><!-- /.ace-nav -->
      </div><!-- /.navbar-header -->
    </div><!-- /.container -->
  </div>

  <div class="main-container" id="main-container">
    <script type="text/javascript">
    try {
      ace.settings.check('main-container', 'fixed')
    } catch (e) {
    }
    </script>

    <div class="main-container-inner">
      <a class="menu-toggler" id="menu-toggler" href="#">
        <span class="menu-text"></span>
      </a>

      <div class="main-content" style="margin-left: 0;">
        <div class="page-content">
          <?php
          if (file_exists(APPPATH . 'views/' . $partial . '.php')) {
            $this->load->view($partial);
          } else {
            $this->load->view("errors/404");
          }
          ?>
        </div><!-- /.page-content -->
      </div><!-- /.main-content -->
    </div><!-- /.main-container-inner -->

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
      <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true"> </div>
    <div class="alert alert-info fade in" id="loading"> <strong>Loading...!</strong></div>
    <div class="alert alert-info fade in" id="loading-save"> <strong>Saved!</strong></div>
  </div><!-- /.main-container -->


  <?php $this->load->view('modal/check_modal'); ?>



  <!-- basic scripts -->

  <!--[if !IE]> -->

  <script type="text/javascript">
  window.jQuery || document.write("<script src='<?php echo base_url('assets/ace/js/') ?>/jquery-1.10.2.min.js'>" + "<" + "/script>");
  </script>

  <!-- <![endif]-->

  <!--[if IE]>
  <script type="text/javascript">
  window.jQuery || document.write("<script src='<?php echo base_url('assets/ace/js/') ?>/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
if ("ontouchend" in document)
document.write("<script src='<?php echo base_url('assets/ace/js/') ?>/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>
<script src="<?php echo base_url('assets/ace/js/') ?>/bootstrap.min.js"></script>
<script src="<?php echo base_url('assets/ace/js/') ?>/typeahead-bs2.min.js"></script>

<!-- page specific plugin scripts -->

<!--[if lte IE 8]>
<script src="<?php echo base_url('assets/ace/js/') ?>/excanvas.min.js"></script>
<![endif]-->

<script src="<?php echo base_url('assets/ace/js/') ?>/jquery-ui-1.10.3.custom.min.js"></script>


<!-- ace scripts -->


<script src="<?php echo base_url('assets/app/js/jquery-ui.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/dateFormat.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/jquery.dateFormat.js') ?>"></script>
<script src="<?php echo base_url('assets/ace/js/') ?>/ace-elements.min.js"></script>
<script src="<?php echo base_url('assets/ace/js/') ?>/ace.min.js"></script>

<script src="<?php echo base_url('assets/app/js/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?php echo base_url('assets/ace/js/fullcalendar.min.js') ?>"></script>


<script src="<?php echo base_url('assets/app/js/jquery.inputmask.bundle.min.js') ?>"></script>
<script src="<?php echo base_url('assets/app/js/prettyCheckable.min.js') ?>"></script>
<!-- jcm -->
<script src="<?php echo base_url('assets/app/js/clinical_trigger_api.js') ?>"></script>


<?php
$current_date = ($this->session->userdata('CURRENT_CALENDAR')) ? $this->session->userdata('CURRENT_CALENDAR') . 'T00:00:00' : NULL;
$dy = explode('-', $current_date);
$y = ($dy && isset($dy[0])) ? (int) $dy[0] : (int) date('Y');
$m = ($dy && isset($dy[1])) ? (int) $dy[1] - 1 : ((int) date('m') - 1);
$d = ($dy && isset($dy[2])) ? (int) $dy[2] : (int) date('d');
?>
<script>
var mysite = '<?php echo base_url(); ?>index.php/';

var cuurent_date = false, current_year = false, current_month = false, current_day = false;
cuurent_date = '<?php echo $current_date ?>';
current_year = <?php echo $y; ?>;
current_month = <?php echo $m; ?>;
current_day = <?php echo $d; ?>;

</script>
<script src="<?php echo base_url('assets/app/js/app.js?' . date('YmdHis')) ?>"></script>
<!-- <script src="<?php echo base_url('assets/app/js/calendar_js.js?' . date('YmdHis')) ?>"></script> -->
</body>
</html>

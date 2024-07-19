<?php
$user = $this->mylib->dt_current_user();
$title_client = 'WellTrackONE';
if($user){
  $db = $this->data_db = $this->load->database('image', TRUE);
  $client = $db->from($this->ClientImagesModel->table)->select('ClientName')
  ->where(array( 'Hidden' => 0, 'Org_ID' => $user->Org_Id )) ->get()->row();
  if($client){
    $title_client = $client->ClientName;
  }
}

?>
<div class="navbar navbar-default" role="navigation" id="">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo site_url('dashboard') ?>"><?php echo $title_client?></a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav  navbar-right">
        <?php if ($this->sessionlib->check_login()) { ?>
          <li><a href="<?php echo site_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><?php echo anchor('schedule', 'Schedule') ?></li>
              <li><?php echo anchor('patient', 'Patients') ?></li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hi, <?php echo $this->mylib->dt_current_user()->Fname . ' ' . $this->mylib->dt_current_user()->Lname ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><?php echo anchor('session/logout', 'LogOut') ?></li>
            </ul>
          </li>
        <?php } else { ?>
          <li ><a href="<?php echo base_url() ?>">Login</a></li>
        <?php } ?>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

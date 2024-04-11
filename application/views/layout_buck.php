<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>WellTrackONE <?php echo (isset($title)) ? $title : "Wellness Propgram for better health" ?></title>
    <?php $this->load->view('common/load_css'); ?>
  </head>
  <body>
    <div id="wrapper">
      <?php $this->load->view('common/navbar'); ?>
      <div id="content">
        <?php $this->load->view($partial); ?>
      </div>
      <?php $this->load->view('common/footer'); ?>
      <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true"> </div>
      <div class="alert alert-info fade in" id="loading"> <strong>Loading...!</strong></div>
      <?php $this->load->view('common/load_js'); ?>
    </div>
  </body>
</html>

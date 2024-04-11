<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title><?php echo $this->mylib->get_client()?></title>
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
        display: none;
      }

      @media print{
        .print-area_dv{
          display: block;
        }
      }
    </style>
  </head>
  <body>
    <div class="print-area_dv">
      <?php $this->load->view($partial); ?>
    </div>

    <script src='<?php echo base_url('assets/ace/js/') ?>/jquery-1.10.2.min.js'></script>
    <script>
      var document_focus = false;
      $(window).on('load', function() {
        "use strict";
        window.print();
        document_focus = true;
      });
      setInterval(function() {
        if (document_focus === true) {
          window.close();
        }
      }, 300)
    </script>
  </body>
</html>

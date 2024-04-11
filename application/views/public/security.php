<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="press_a_key">
        <?php
        echo '<p class="text-center">Sorry, you have exceeded the number of allowable login attempts.</p>';
        if ($this->agent->is_mobile()) {
          echo anchor("session/reset_security", "<p class='text-center'><strong>Press this to continue</strong></p>", "class='text-danger'");
        }else{
          echo "<p class='text-center'><strong>Press any key to continue</strong></p>";
        }
        ?>
      </div>
    </div>
  </div>
</div>


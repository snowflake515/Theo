<style>
  .cstm-cb{
    margin-bottom: 20px;
    color: #428bca;
  }
  
  .btn-sm{
    font-size: 20px;
    height: 47px;
  }
  .sub-title{
    font-size: 24px;
    color: #428bca;
    margin-top: 20px;
  }

  .cstm-cb input[type="checkbox"], .cstm-cb input[type="radio"]{
    visibility: hidden;
  }

  .cstm-cb input[type="text"]+span,  .cstm-cb input[type="checkbox"]+span, .cstm-cb input[type="radio"]+span{
    width: 30px;
    top: 5px;
    position: relative;
    display: inline-block;
    margin-left: -20px;
    margin-right: 10px;
  }

  .cstm-cb input[type="checkbox"]+span .icon, .cstm-cb input[type="radio"]+span .icon{
    font-size: 30px;
  }
  .cstm-cb.no-margin{
    margin-bottom: 0;
  }
  .cstm-cb input[type="checkbox"]+span .icon-check, .cstm-cb input[type="checkbox"]:checked+span .icon-unchecked,
  .cstm-cb input[type="radio"]+span .icon-circle, .cstm-cb input[type="radio"]:checked+span .icon-circle-blank
  {
    display: none;
  }
  .cstm-cb input[type="checkbox"]:checked+span .icon-check, .cstm-cb input[type="radio"]:checked+span .icon-circle{
    display: inline-block;
  }

  .cstm-cb input[type="radio"]:checked+span .icon-circle{
  }

  .wrap-tml-con{
    position: absolute; width: 100%; height: 100%; top: 0; left: 0; background: rgba(0,0,0,0.05);
    z-index: 10;
  }

  .widget-cstm .widget-body{
    position: relative;
  }
  
  h4 {
    font-size: 24px;
  }
  .widget-cstm .help-block{
    font-size: 16px;
    padding-left: 30px;
  }

  .widget-cstm.loading .widget-header small.label{
    display: inline-block !important;
  }

  .widget-cstm.loading .widget-body .wrap-tml-con{
    display: block !important;
  }

  .f-cstm-input{
    display: inline-block; 
    width: 80%;
    height: 42px;
    font-size: 24px;
  }

  .msg-notif{
    position: fixed;
    z-index: 1007;
    right: 20px;
    top: 53px;
    background: yellow;
    font-weight: 700;
    font-size: 18px;
    padding: 8px 20px;
    box-shadow: 0px 2px 2px rgba(0,0,0,0.2);
  }

  .dcmh{
    min-height: 0 !important;
    max-height: 100% !important;
  }

  @media (max-width: 1200px) {
     /*.dcmh{
       min-height: 0 !important;
       max-height: 100% !important;
     }*/
  }

  .btn-t.collapsed .c{
    display: none;
  }

  .btn-t:not(.collapsed ) .o{
    display: none;
  }

  label {
    font-size: 20px;
    font-weight: normal;
  }
  label.locked{
    position: relative;
    display: block;
    pointer-events: none;
  }

  label.locked:after{
    content: "-- Locked";
    position: absolute;
    left: -21px;
    padding: 0 15px;
    background: #fff;
    /* width: 100%; */
    color: #ddd;
    /* height: 100%; */
    top: 0;
    bottom: 0;
    right: 0;

  }

</style>
<script>
  var flag = 0;
  function onSave() {
    $('#digitPassword').modal('show');
    flag = 1;
  }
  function onBack() {
    $('#digitPassword').modal('show');
    flag = 0;
  }
  var site_url = '<?php echo site_url() ?>/';

  function change_new_tml1(el) {
    var $this = $(el);
    var is_continue = true; 
    // if (localStorage.getItem('data')) {
    //   is_continue = false;
    // }
    if($this.find('option:selected').text().includes("[Deleted] ---")){
      var is_continue = confirm("The Template for this Encounter has been deleted. Do you want to continue using it?"); 
      if(!is_continue){
        $this.val('');
        $('#wrap_tml2').html('');
        $('#wrap_tml3').html('');
      }
    }
	
    if(is_continue){
      $('.msg-notif').text('Please Wait...');
      open_loading(true);
      localStorage.setItem('tml1_ID', $this.val());
      localStorage.setItem('Encounter_ID', $('#Encounter_ID').val());
      $('.msg-notif').removeClass('hide');
      $.ajax({
        type: "POST",
        url: site_url + "template_v2/change_tml1",
        data: {tml1_ID: $this.val(), Encounter_ID: $('#Encounter_ID').val()},
        success: function (data) {
            localStorage.setItem('data', JSON.stringify(data));
            $('#wrap_tml2').html(data.tml2);
            $('#wrap_tml3').html(data.tml3);
          
            var phq2 = data.phq2_total;
              
            check_phq(phq2); 
            open_loading(false);
            $('.msg-notif').addClass('hide');
            init_mask();
            //show or hide btn save
            if(data.tml3){
              $('#btn-sv').removeClass('hide');
            }else{
              $('#btn-sv').addClass('hide');
            }

            if(data.theo_session_id){
              $('#btn-gp').removeClass('hide');
              $('#btn-gp').attr('href', $('#btn-gp').attr('originurl') +'/'+ data.theo_session_id);
            }else{
              $('#btn-gp').addClass('hide');
            }
        },
          error: function (ress, status, error) {
          $('.msg-notif').addClass('hide');
          my_handle_error(ress)
          }
      });
	  }
  }

  function change_new_tml2(el) {
    var prefix = '#tml2-wrap';
    var id = $(el).val();
    var $target =prefix+'-'+id;

    if($(el).is(":checked")){
      $($target).removeClass('hide');
    }else{
      $($target).addClass('hide');
    }

  }

  function change_new_tml3(el) {
    $('.msg-notif').text('Saved');
    var $this = $(el),
        tml3_id = $this.data('tml3id'),
        tml2_id = $this.data('tml2id'),
        theoquestion_id = $this.data('theoquestion_id'),
        theoanswer_id = $this.data('theoanswer_id'),
        theovideoplay_id = $this.data('theovideoplay_id'),
        theosession_id = $this.data('theosession_id'),
        theoaccount_id = $this.data('theoaccount_id'),
        is_checked = ($('#cbx_' + tml3_id).is(':checked')) ? 1 : 0,
        input_val = null;

    if ($('#input_' + tml3_id).length) {
      input_val = $('#input_' + tml3_id).val();
    }

    if (is_checked == 0) {
      $('#input_' + tml3_id).val('');
      input_val = null;
    }

    $('#modal_InstructionLabel').text('Loading...');
    $('#modal_tml3_id').val('');
    $('#val_insructure').val('');

    $.ajax({
      type: "POST",
      url: site_url + "template_v2/save_tml3",
      data: {
        tml3_ID: tml3_id,
        tml2_ID: tml2_id,
        tml1_ID: $('#TML1_ID').val(),
        checked: is_checked,
        input: input_val,
        Encounter_ID: $('#Encounter_ID').val(),
        theoquestion_ID: theoquestion_id,
        theoanswer_ID: theoanswer_id,
        theosession_ID: theosession_id,
        theovideoplay_ID: theovideoplay_id,
        theoaccount_ID: theoaccount_id,
      },
      success: function (data) {
        open_loading(false);
        $('input[data-tbotmaster="431"]').val(data.phq9_total);
        $('input[data-tbotmaster="1147"]').val(data.phq2_total);

        var phq2 = data.phq2_total;
        console.log(phq2);
        check_phq(phq2);

 
        if ($this.data('enableins') == 1 && is_checked == 1) {
          var labl = $('#tm3_InstructionLabel_' + tml3_id).attr('value');
          $('#modal_template').modal('show');
          setTimeout(function(){
            $('#modal_tml3_id').val(tml3_id);
            $('#modal_InstructionLabel').text(labl);
            $('#val_insructure').focus();
          }, 1000);
        }

        if ($this.data('forceselect') != "" && is_checked == 1) {
          var force_select = $('#wrap_tml3 [data-pforceselect="'+$this.data('forceselect')+'"]');
          if (force_select.length && force_select.is(':checked') == false) {
            force_select.trigger('click');
          }
        }
        $('.msg-notif').addClass('hide');
      },
      beforeSend: function () {
        open_loading(true);
        $('.msg-notif').removeClass('hide');
      },
      error: function (ress, status, error) {
        $('.msg-notif').addClass('hide');
        my_handle_error(ress);
      }
    });
  }

  function change_new_tml3_input(el) {
    var tml3_id = $(el).data('tml3id'), checkbox;

    if ($.trim($(el).val()) != $(el).attr('data-oldval')) {
      if ($.trim($(el).val())) {
        checkbox = $('#cbx_' + tml3_id).prop({checked: true});
      } else {
        checkbox = $('#cbx_' + tml3_id).prop({checked: false});
      }
      change_new_tml3(checkbox);
    }

  }

  function open_loading($bol) {
    if ($bol) {
      $('select#TML1_ID').attr('disabled', 'disabled');
      $('div#container-tml3, div#container-tml2').addClass('loading');
    } else {
      $('select#TML1_ID').removeAttr('disabled');
      $('div#container-tml3, div#container-tml2').removeClass('loading');
    }
  }

  function init_mask() {
    $('#wrap_tml3 input.form-control[data-mask="realnumber"]').attr({
    'type': 'number',
    'step': '0.01'
    }).on('input', function() {
    var value = $(this).val();
    if (value.length > 4) {
        $(this).val(value.slice(0, 5));
    }
    });
    $('#wrap_tml3 input.form-control[data-mask="integer"]').attr({
    'type': 'number',
    'step': '1'
    }).on('input', function() {
    var value = $(this).val();
    if (value.length > 3) {
        $(this).val(value.slice(0, 3));
    }
    });

    $('#wrap_tml3 input.form-control[data-mask="alphanumeric"]').inputmask('Regex', {regex: "^[a-zA-Z0-9 ,;]+$/"});
    $('#wrap_tml3 input.form-control[data-mask="letters_only"]').inputmask('Regex', {regex: "^[A-Za-z ]*[A-Za-z ][A-Za-z ]*$"});
  }

  function check_old_val(el) {
    $(el).attr('data-oldval', $(el).val());
  }

  function insert_answer_instr(el) {
    var
      $tml3_id = $('#modal_tml3_id').val(),
      $val_ans = $('#val_insructure').val()
    if ($val_ans) {
      $('#li_tml3_' + $tml3_id).next('li').find('input[type="text"]').val($val_ans).trigger('blur');
      $('#modal_template').modal('hide');
    } else {
      alert('Please insert your answer!');
    }

  }

  function my_handle_error(ress) {
    if (ress.status == 401) {
      alert(ress.statusText);
      window.location = site_url;
    }
  }

  function check_phq(phq2){
    if(phq2 >= 3){ 
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="360"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="385"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="386"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="387"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="388"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="387"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="389"]').closest("label").removeClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="390"]').closest("label").removeClass("locked");
    }else{
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="360"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="385"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="386"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="387"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="388"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="387"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="389"]').closest("label").addClass("locked");
      $('#wrap_tml3').find('[data-tml3_tbotmaster_id="390"]').closest("label").addClass("locked");
    }  
  }

  let correctPin = "1234";

  let btns =
    document.getElementsByClassName(
      "pinpad-btn"
    );
  let pinInput = document.getElementById(
    "pinpad-input"
  );
  for (let i = 0; i < btns.length; i++) {
    let btn = btns.item(i);
    if (
      btn.id &&
      (btn.id === "submit-btn" ||
        btn.id === "delete-btn")
    )
      continue;

    // Add onclick event listener to 
    // Every button from 0 - 9
    btn.addEventListener(
      "click",
      (e) => {
        pinInput.value +=
          e.target.value;
        if (pinInput.value.length == 4) {
          onPIN();
          return;
        }
      }
    );
  }

  let submitBtn = document.getElementById(
    "submit-btn"
  );
  let delBtn = document.getElementById(
    "delete-btn"
  );
  let modal =
    document.getElementById("modal");
  let result =
    document.getElementById("result");
  let closeBtn =
    document.getElementById("close");

  function onPIN() {
    if (!pinInput || !pinInput.value || pinInput.value === "" ) {
      alert( "Please enter a pin first");
      pinInput.value = "";
      return;
    }
    $.ajax({
        url: mysite + "/user_password/check_pin", 
        type: 'POST',
        data: {
          pin: pinInput.value
        }, 
        success: function(data) {
          if (data === 'true') {
            if (flag == 1) {
              saveData();
            }else{
              window.location.href = site_url + 'encounter/start/' + $('#Appointment_ID').val();
            }
            return;
          }else{
            alert( "Please enter your pin correctly!");
            pinInput.value = "";
            return;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error: ' + textStatus, errorThrown);
        }
    });
  }
    
  function saveData() {
    $.ajax({
        url: mysite + "/clinical_trigger/save_update_data", 
        type: 'POST',
        data: {
          app_id: $('#app_id').val(),
          en_id: $('#en_id').val(),
          pa_id: $('#pa_id').val(),
          pro_id: $('#pro_id').val(),
          org_id: $('#org_id').val(),
          end_id: $('#end_id').val(),
        }, 
        success: function(data) {
          console.log($('#app_id').val(), $('#en_id').val());
          if (data == 1) {
            window.location.href = site_url + 'encounter/start/' + $('#Appointment_ID').val();
            return;
          }else{
            alert( "Fail! Please try again");
            pinInput.value = "";
            return;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error: ' + textStatus, errorThrown);
        }
    });
  }

  delBtn.addEventListener("click", () => {
    if (pinInput.value)
      pinInput.value =
        pinInput.value.substr(
          0,
          pinInput.value.length -
            1
        );
  });

  closeBtn.addEventListener(
    "click",
    () => {
      modal.style.display = "none";
    }
  );
</script>

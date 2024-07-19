var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
var temp = date.getTime();

$(document).ready(function () {
  change_placeholder_search_patient();
  ajax_modal();
  call_full_calendar();
  select_provider();
  need_confrim();
  datetime_picker();
  popup_page();
  press_a_key();
  date_picker();
  time_picker();
  input_check();
  answer_templeate();
  encounter_select();
  default_date_pick();

  if ($('*[data-toggle=tooltip]').length > 0) {
    $('*[data-toggle=tooltip]').tooltip({
      container: 'body'
    });
  }


  if ($('#view_calendar').length > 0) {
    $('#view_calendar').change(function () {
      change_view_calendar(this);
    });
  }

  if (cuurent_date) {
    change_php_datecalendar(current_year, current_month, current_day);
  }

  $('.tab_patients').click(function (e) {
    var id = $(this).attr("id");

    $('input[name=next_form]').val(id);
    $("#patient_form").submit();
    return false;
  });

  call_set_pagination();
 
  checkBox();
  cliked_events();

  $('.ajax_print').click(function () {
    var newWin = window.open('', 'my print');
    $.ajax({
      type: "GET",
      url: $(this).attr('data-target'),
      error: function () {
        // show_loading(true);
        alert('Failed Load Page');
        newWin.close();
      },
      success: function (data) {
        newWin.document.write(data);
        newWin.document.close(); //missing code
        newWin.focus();
        newWin.print();
        newWin.close();
      }
    });
    return false;
  })
});

function cliked_events() {
  
  $('#generate-en-report').click(function () {

    var el = $(this);
    if (el.hasClass('active')) {
      return false;
    }else{
      el.addClass('active').html('<span style="color:yellow">Generating Report !</span>');
      $.ajax({
        type: "POST",
        url: mysite + 'api/check_login?buster=' + new Date().getTime(),
        success: function (data) {
          if (data == "") {
            $.ajax({
              type: "POST",
              url: el.attr('href') + '?buster=' + new Date().getTime(),
              data: {
                EncounterDate: $('#EncounterDate').val(),
                ChiefComplaint: $('#ChiefComplaint').val(),
                EncounterDescription_ID: $('#EncounterDescription_ID').val(),
                Provider_ID: $('#select_provider').val(),
                SupProvider_ID: $('#select_supprovider_id').val(),
                Dept_ID: $('#Dept_ID').val(),
                Appointments_ID: $('#Appointments_ID').val(),
                Encounter_ID: $('#Encounter_ID').val(),
                Facility_ID: $('#Facility_ID').val()
              },
              success: function (data) {
                data = jQuery.parseJSON(data);
                el.removeClass('active').html('Generate Report');
                $('#list_encounter_history li.active span').text($('#ChiefComplaint').val());
                alert(data.msg);
              }
            });
          }else{
            check_session(data);
          }
          
        }
      });
      return false;
    }
  });
}

function call_set_pagination() {
  $(".pagination_patients a").each(function () {
    var href = $(this).attr('href');
    var params = "";
    if ($('#current_time').val() != "" || $('#current_select').val() != "") {
      params = '?current_select=' + $('#current_select').val() + '&current_time=' + $('#current_time').val();
    }
    $(this).attr('href', href + params);
  });
}

function default_date_pick() {
  $('#starttime').change(function () {
    var currennt_start = $(this).val();
    currennt_start = currennt_start.split(":");
    var temp_minute = currennt_start[1].split(' ');
    var dateappt = $('#dateappt').val();
    var dateappt = new Date
    dateappt.setHours(0, 0, 0, 0);
    if (temp_minute[1] == 'pm') {
      dateappt.setHours(parseInt(currennt_start[0]) + 12);
    } else {
      dateappt.setHours(currennt_start[0]);
    }
    dateappt.setMinutes(temp_minute[0]);
    dateappt.setMinutes(dateappt.getMinutes() + 30);
    //$('#stoptime').val(convert_two_digit(dateappt.getHours()) + ":" + convert_two_digit(dateappt.getMinutes()));
//    if(dateappt.getHours() > 12 ){
//      $('#stoptime').val(convert_two_digit(dateappt.getHours() - 12) + ":" + convert_two_digit(dateappt.getMinutes()) +' pm');
//    }else{
//      $('#stoptime').val(convert_two_digit(dateappt.getHours()) + ":" + convert_two_digit(dateappt.getMinutes())  +' am');
//    }
    $('#stoptime').val($.fullCalendar.formatDate(dateappt, "hh:mm tt"))

  });
}

function convert_two_digit(value) {
  if (value.toString().length == 1) {
    value = "0" + value
  }
  return value;
}

function checkBox() {

  var inputs = $('input.checkBox').each(function () {
    $(this).prettyCheckable({
      labelPosition: 'right'
    });
  });

  var inputs = $('label').each(function () {
    if ($(this).text() == "[INPUT]" || $(this).text() == "[Input]" || $(this).text() == "[input]") {
      $(this).hide();
    }
  });
}

function ajax_modal() {
  if ($('.ajax_link').length > 0) {
    $('.ajax_link').click(function (e) {
      $('#modal').html("").removeData("bs.modal").modal({
        remote: $(this).attr("href")
      });
      return false;
    });
  }

}

function need_confrim() {
  if ($(".need_confrim").length > 0) {
    $('.need_confrim').click(function () {
      var text = ($(this).data('text_comfrim')) ? $(this).data('text_comfrim') : "Are you sure?";
      var cek = confirm(text);
      return cek;
    });
  }

}

function datetime_picker() {
  if ($(".datetimepicker").length > 0) {
    $(".datetimepicker").datetimepicker({
      dateFormat: "yy-mm-dd "
    });
  }
}

function time_picker() {
  if ($(".timepicker").length > 0) {
    $(".timepicker").datetimepicker({
      timeOnly: true,
      timeFormat: 'hh:mm tt',
      controlType: 'select'
    });
  }

  if ($("#starttime").length > 0) {
    $("#starttime").datetimepicker({
      timeOnly: true,
      stepMinute: 5,
      timeFormat: 'hh:mm tt',
      controlType: 'select'
    });
  }

  if ($("#stoptime").length > 0) {
    $("#stoptime").datetimepicker({
      timeOnly: true,
      stepMinute: 10,
      timeFormat: 'hh:mm tt',
      controlType: 'select'
    });
  }
}

function date_picker() {
  if ($('#dob').length > 0) {
    $('#dob').inputmask({"mask": "m/d/y", 'autoUnmask': true});
  }
  if ($(".datepicker").length > 0) {
    $(".datepicker").datepicker({
      dateFormat: "mm-dd-yy",
      changeMonth: true,
      changeYear: true,
      beforeShow: function () {
        setTimeout(function () {
          $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
      }
    });
  }
  if ($('.date-form').length > 0) {
    $('.date-form').inputmask({"mask": "m/d/y", 'autoUnmask': true});
  }

}

function change_status_chekin_data($this) {
  var dt = $($this);
  if( dt.val() != "" ){
    $.ajax({
      type: "POST",
      url: mysite + 'api/check_log',
      data: {
        Appointments_Id: dt.data('apptid'),
        CodeOrder: dt.val()
      },
      beforeSend: function () {
        show_loading(true);
      },
      success: function (data) {
        show_loading(false);
        $(dt.data('color')).css('background', '#' + data);
        if ($('#calendar').length == 0) {
          check_session(data);
        }
        refesh_calendar();
      }
    });
  } 
}

function call_full_calendar() {
  if ($('#select_provider_calendar').length > 0) {
    $('#select_provider_calendar').change(function () {
      refesh_calendar();
    });
  }

  if ($('#change_date').length > 0) {
    $('#change_date').change(function () {
      if ($(this).val() != null) {
        var current_call_date = $(this).val();
        var slp = current_call_date.split('-');
        var current_year = parseInt(slp[2]), current_month = parseInt(slp[0]) - 1, current_day = parseInt(slp[1]);
        $('#current_select').val('');
        $('#calendar').fullCalendar('changeView', 'agendaDay');
        $('#view_calendar').val('agendaDay');
        $('#view_calendar').trigger('change');
        //change_date_calendar(c_date);
        change_php_datecalendar(current_year, current_month, current_day);
      }
    });
  }

  if ($('#calendar').length > 0) {
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        right: 'title'
      },
      defaultView: 'agendaDay',
      //  defaultView: 'month',
      editable: false,
      slotEventOverlap: false,
      slotMinutes: 30,
      dayClick: function (date, allDay, jsEvent, view) {
        show_loading(true);
        // window.location.href = mysite + 'appointment';
        date_select = $.fullCalendar.formatDate(date, "MM-dd-yyyy")
        $('#current_select').val(date_select);
        $('#current_time').val($.fullCalendar.formatDate(date, "hh:mm tt"))
        $('#form_appt_get').submit();
      },
      eventRender: function (event, element, view) {
        var html = $.parseHTML(event.title);
        if (view.name == "agendaDay") {
          element.find('.fc-event-inner').html(html);
        } else {
          element.find('.fc-event-title').html(html);
        }
        element.find('a').click(function () {
          window.location = $(this).attr('href');
          return false;
        });
      },
      eventClick: function (calEvent, jsEvent, view) {
        if (view.name == "month") {
          date = $.fullCalendar.formatDate(calEvent.start, "MM-dd-yyyy");
          $('#current_select').val(date);
          $('#form_appt_get').submit();
        } else {
          $.ajax({
            type: "POST",
            url: mysite + 'api/check_login?buster=' + new Date().getTime(),
            success: function (data) {
              if (data == "") {
                $('#modal').html("").removeData("bs.modal").modal({
                  remote: mysite + 'schedule/appointment_detail/' + calEvent.id
                });
              }
              check_session(data);
            }
          });
        }
      }
      ,
      events: function (start, end, callback) {
        var xr_provider = $('#select_provider_calendar').val(),
                xr_view = $('#calendar').fullCalendar('getView').name,
                xr_start = start.getFullYear() + '-' + check_lenght(start.getMonth() + 1) + '-' + check_lenght(start.getDate()),
                xr_end = end.getFullYear() + '-' + check_lenght(end.getMonth() + 1) + '-' + check_lenght(end.getDate());
        sycn_form_print_call(xr_provider, xr_view, xr_start, xr_end);
        $.ajax({
          url: mysite + 'api/get_appointments',
          type: 'POST',
          dataType: "json",
          data: {
            provider: xr_provider,
            view: xr_view,
            start: xr_start,
            end: xr_end,
          },
          error: function (data) {
            callback(null);
            $('#alert-calendar').fadeIn();
          },
          success: function (data) {
            $('#alert-calendar').fadeOut();
            callback(data);
            show_loading(false);
            check_session(data);
          }
        });
      },
      loading: function (bool) {
        show_loading(bool);
      }
    });
  }

}

function sycn_form_print_call(xr_provider, xr_view, xr_start, xr_end) {
  $('#xr_provider').val(xr_provider);
  $('#xr_view').val(xr_view);
  $('#xr_start').val(xr_start);
  $('#xr_end').val(xr_end);
}

function get_view() {
  return $('#calendar .fc-header-right .fc-button.ui-state-active').html()
}

function show_loading(bool) {
  var x = (bool) ? $('#loading').show() : $('#loading').hide();
}

function show_loading_save(bool) {
  var x = (bool) ? $('#loading-save').show() : $('#loading-save').hide();
}

function check_lenght(val) {
  return (val.toString().length == 2) ? val : '0' + val
}

function select_provider() {
  if ($('#select_provider').length > 0) {
    var dt = $('#select_provider');
    if (dt.data('action') == "add") {
      run_ecpunter_select(dt);
    }
    $('#select_provider').change(function () {
      var dt = $(this);
      run_ecpunter_select(dt);
    });

    function run_ecpunter_select(dt) {
      if (dt.val() != "") {
        $.ajax({
          type: "POST",
          async: true,
          cache: false,
          url: mysite + 'api/get_encounter_type',
          data: {
            Provider_ID: dt.val(),
            select: dt.data('select'),
            form: dt.data('target')
          },
          beforeSend: function () {
            show_loading(true);

          },
          success: function (data) {
            show_loading(false);
            $(dt.data('target')).html(data);
          }
        });
      }
    }
  }
}

function select_patien(selector) {
  if ($('#patien_name').length > 0 && $('#Patient_ID').length > 0) {
    $('#patien_name').val(selector.data('name'));
    $('#Patient_ID').val(selector.val());
  }
}

function refesh_calendar() {
  if ($('#calendar').length > 0) {
    $('#calendar').fullCalendar('refetchEvents')
  }
}

function change_php_datecalendar(year, month, dat) {
  if ($('#calendar').length > 0) {
    $('#calendar').fullCalendar('gotoDate', year, month, dat);
  }
}

function change_date_calendar(date) {
  if ($('#calendar').length > 0) {
    $('#calendar').fullCalendar('gotoDate', date.getFullYear(), date.getMonth(), date.getDate());
  }
}

function change_view_calendar(selector) {
  if ($('#calendar').length > 0) {
    $('#calendar').fullCalendar('changeView', $(selector).val());
    refesh_calendar();
  }
}

function press_a_key() {
  if ($('.press_a_key').length > 0) {
    $(document).keypress(function (e) {
      window.location = mysite + 'session/reset_security';
    });
  }
}


function answer_templeate() {
  $('input[name="template3[]"]').on("change", function () {


  });
}

function convertDateToUTC(date) {
  return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
}

function save_template3(selector) {
  var Encounter_ID = $('#id_encounter').val();
  var tml1_ID = $('#tml_1').val();
  var TML3_ID = $(selector).val();
  var status = 0;
  var TML3_Value = "";

  if ($('#' + TML3_ID).length > 0) {
    TML3_Value = $('#' + TML3_ID).val();
  }
  if ($(selector).is(':checked')) {
    status = 1;
  } else {
    if ($('#' + TML3_ID).length > 0) {
      $('#' + TML3_ID).val('');
    }
  }

  var data_post = {
    Encounter_ID: Encounter_ID,
    TML3_ID: TML3_ID,
    TML3_Value: (TML3_Value) ? TML3_Value : null,
    TML2_ID: $(selector).data('tml2'),
    TML1_ID: tml1_ID,
    status: status
  };
  save_template(data_post);
}

function save_template(data_post) {
  $.ajax({
    type: "POST",
    url: mysite + "template/save/",
    data: data_post,
    cache: false,
    success: function (data) {
      show_loading_save(false);
      check_session(data);
    },
    error: function (err) {
      show_loading_save(false);
    },
    beforeSend: function () {
      show_loading_save(true);
    }
  });
}


function input_check() {
  if ($('.input_check').length > 0) {
    $('.input_check').blur(function () {
      var dt = $(this), target = dt.data('target'), temp_val = dt.data('tmp');
      if (dt.val() != temp_val) {
        dt.data('tmp', dt.val());
        if (dt.val() == null || dt.val() == "") {
          $(target).prettyCheckable('uncheck');
        } else {
          $(target).prettyCheckable('check');
        }
        save_template3(target);
      } else {

      }
    });
  }
}

function encounter_select() {
  if ($('.select_provider').length > 0) {
    $('#SignedOffSupervising').attr('disabled', 'disabled');
    if ($('#select_provider').val() == "" || ($('#select_provider').attr('disabled') == 'disabled')) {
      $('#Rendering_id').attr('disabled', 'disabled');
    }
    $('.select_provider').change(function () {
      check_it();
    });
    $('#Rendering_id').change(function () {
      check_it();
    });
    $('#select_supprovider_id').change(function () {
      check_it();
    });

    if ($('#check_provider').text() != "" && $('#select_provider').attr('disabled') != "disabled") {
      //$('#SignedOffSupervising').removeAttr('disabled');
    }
    check_it();

  }

}

function check_it() {
  if ($('#select_provider').val() == "") {
    $('#Rendering_id').attr("checked", false);
  }
  if (($('#select_provider').val() == "") || ($('#select_provider').attr('disabled') == 'disabled')) {
    $('#Rendering_id').attr('disabled', 'disabled');
  } else {
    $('#Rendering_id').removeAttr('disabled');
  }
  if ($('#Rendering_id').is(':checked') == true) {
    if ($('#select_provider').val() != "" && $('#select_supprovider_id').val() != "") {
      if ($('#select_supprovider_id').attr('disabled') == 'disabled') {
        $('#SignedOffSupervising').attr('disabled', 'disabled');
      } else {
        $('#SignedOffSupervising').removeAttr('disabled');
      }

    } else {
      disable_check();
    }
  } else {
    //if (($('#check_provider').text() == "")) {
    disable_check();
    //}
  }

  function disable_check() {
    $("#SignedOffSupervising").attr("checked", false);
    $('#SignedOffSupervising').attr('disabled', 'disabled');
  }
}
$('.select_provider').change(function () {

  //      if (($('#select_provider').val() == $('#select_supprovider_id').val()) || ($('#select_supprovider_id').val() == "" && $('#Rendering_id').is(':checked') == false)) {
  //        $('#SignedOffSupervising').removeAttr('disabled');
  //      } else {
  //        $('#SignedOffSupervising').attr('disabled', 'disabled');
  //      }
  //      if ($('#select_supprovider_id').val() == "" && $('#Rendering_id').is(':checked') == false) {
  //        $('#SignedOffSupervising').attr('disabled', 'disabled');
  //      }
  //      $('#select_supprovider_id').change(function() {
  //        if ($('#select_supprovider_id').val() == "" || $('#Rendering_id').is(':checked') == false) {
  //          $('#SignedOffSupervising').attr('disabled', 'disabled');
  //        }
  //      });
});


function change_placeholder_search_patient() {
  $('#by_field').change(function () {
    var t = document.getElementById("by_field");
    var selectedText = t.options[t.selectedIndex].text;

    $("#text_field").attr("placeholder", "Search By " + selectedText);
  });

}

function printThis(el) {
//  var doc = new jsPDF();
//  var source = $('#render_me').addClass('print').get(0);
//  doc.addHTML(source,
//          function() {
//            doc.save($(el).data('name') + '.pdf');
//            $('#render_me').removeClass('print');
//          });
//

}

function popup_page() {
  if ($('.popup').length > 0) {
    $('.popup').click(function () {
      var params = [
        'height=' + (screen.height - 50),
        'width=' + screen.width,
        'resizable=yes',
        'scrollbars=yes'
      ].join(',');
      var a = window.open($(this).data('target'), "Patient", params);
      a.focus();
    });
  }
}

function check_session(data) {
  if (data.error == true) {
    alert(data.msg);
    window.location.href = mysite;
    return;
  }
}


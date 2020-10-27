import Vue from 'vue'
import 'jquery-form/dist/jquery.form.min.js';
import 'datatables'
import 'toastr/build/toastr.min.css';
import toastr from 'toastr/build/toastr.min.js';


export default (function () {

  toastr.options = {
    "closeButton": true,
    "escapeHtml": true,
    "progressBar": true,
    "timeOut": 7000
  };

  var app = new Vue({
    el: '#app',
    data: {
      baseUrl: GpApp.baseUrl,
      typeTo: null,
      typeFrom: null,
    },
    methods: {

    }
  });

  function showResponse(responseText, statusText, xhr, $form) {
    var color = 'text-success';
    var formatter = new Intl.NumberFormat('uk', {
      style: 'currency',
      currency: 'GBP'
    });

    $form.find('[type=submit],submit').prop('disabled', false);

    if (responseText.error) {
      toastr.error(responseText.error, 'Error');
    } else {
      toastr.success(responseText.data, 'Success');

      if (responseText.balance <= 0) {
        color = 'text-danger';
      }

      $('#totalBalance').html("<span class='" + color + "'>£" + formatter.format(responseText.balance) + "</span>");

      datatable.DataTable().ajax.reload();
      $($form).resetForm();
      $($form).resetForm();
      $($form).clearForm();
      $($form).parsley().reset();
      $(responseText.trigger).modal('toggle');
    }
  }

  $('#credit-note-form, #debit-note-form, #transfer-form').on('submit', function (e) {
    debugger;
    if ($(this).parsley().isValid()) {
      $(this).ajaxSubmit({
        success: showResponse
      });
    }

    e.preventDefault();

    return false;
  });


  // ------------------------------------------------------
  // @Line Charts
  // ------------------------------------------------------

  if ($('#balance-bar-chart').length > 0) {
    $('#balance-bar-chart').sparkline([2, 5, 6, 10, 9, 12, 4, 9], {
      type: 'bar',
      height: '20',
      barWidth: '3',
      resize: true,
      barSpacing: '3',
      barColor: '#9675ce',
      label: ['q', 'q', 'q', 'q', 'q', 'q', 'q', 'q']
    });
  }

}())
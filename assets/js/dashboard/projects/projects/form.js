import 'jodit/build/jodit.min.css'
import 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'
import { cropImageAndUpload } from '../../../utils/images'
import { uploadDocument } from '../../../utils/documents'
import { Jodit } from 'jodit';
import 'toastr/build/toastr.min.css'
import toastr from 'toastr/build/toastr.min.js'
import places from 'places.js'

$('#btn-delete').click(function (e) {
    e.preventDefault()
    $('#remove-modal').modal('show')
    // $("#form-delete").submit()
    // return false
})

$("#form-delete").on("submit", function () {
    if (confirm(MSG_DELETE_ITEM)) {
        return true
    } else {
        $('form-delete').find('[type=submit],submit').prop('disabled', false)
        return false
    }
})

const applyCropperToInput = () => {
    const inputs = document.querySelectorAll('input[data-preview]')
    inputs.forEach(input => {
        if (input.attributes.initialized) {
            return
        }
        input.attributes.initialized = true
        input.onchange = () => {
            const loading = document.querySelector(input.dataset.loading)
            $(loading).show()
            cropImageAndUpload(input, (result, event) => {
                if (result.response && result.response.files && result.response.files.length) {
                    const url = result.response.files[0].url
                    const preview = document.querySelector(input.dataset.preview)
                    const target = document.querySelector(input.dataset.target)
                    if (preview) {
                        preview.src = url
                    }
                    if (target) {
                        target.value = url
                    }
                    $(loading).hide()
                }
            })
        }
    })
}

const applyDocumentUploadToInput = () => {
    const inputs = document.querySelectorAll('input.document-path')
    inputs.forEach(input => {
        if (input.attributes.initialized) {
            return
        }
        input.attributes.initialized = true 
        input.onchange = () => {
            const loading = document.querySelector(input.dataset.loading)
            $(loading).show()
            uploadDocument(input, (result, event) => {
                if (result.response && result.response.files && result.response.files.length) {
                    const url = result.response.files[0].url
                    const target = document.querySelector(input.dataset.target)
                    const download = document.querySelector(input.dataset.download)
                    download.href = url;
                    $(download).show()
                    if (target) {
                        target.value = url
                    }
                    $(loading).hide()
                }
            })
        }
    })
}

$('[data-prototype]').collection({
    onadd() {
        applyCropperToInput()
        applyDocumentUploadToInput()
    }
});

applyCropperToInput()
applyDocumentUploadToInput()

var creditTransactions;
var prototypeCreditTransactions;
var debitTransactions;
var prototypeDebitTransactions;
var colorIb;
var colorAp;

toastr.options = {
    "closeButton": true,
    "escapeHtml": true,
    "progressBar": true,
    "timeOut": 7000
};

function showResponse(responseText, statusText, xhr, $form) {
    var color = 'text-success';
    var formatter = new Intl.NumberFormat('en-UK', {
        style: 'currency',
        currency: 'GBP'
    });

    $form.find('[type=submit],submit').prop('disabled', false);

    if (responseText.error) {
        toastr.error(responseText.error, 'Error');
    } else {
        toastr.success(responseText.data, 'Success');

        if (responseText.investmentBalance <= 0) {
            colorIb = 'text-danger';
        }

        if (responseText.projectProfit <= 0) {
            colorAp = 'text-danger';
        }

        $('#investmentBalance').hide()
        $('#projectProfit').hide()

        $('#investmentBalance').html("<span class='" + color + "'>" + formatter.format(responseText.investmentBalance) + "</span>")
            .fadeIn(1000);
        $('#projectProfit').html("<span class='" + color + "'>" + formatter.format(responseText.projectProfit) + "</span>")
            .fadeIn(1000);

        projectTransactionsDatatable.DataTable().ajax.reload();

        $('#credit-container').find("tbody").empty();
        $('#credit-container').data('index', 0);
        $('#debit-container').find("tbody").empty();
        $('#debit-container').data('index', 0);

        $($form).resetForm();
        $($form).resetForm();
        $($form).clearForm();
        $($form).parsley().reset();
        $(responseText.trigger).modal('toggle');



    }
}

$('#credit-note-form, #debit-note-form').on('submit', function (e) {
    if ($(this).parsley().isValid()) {
        $(this).ajaxSubmit({
            success: showResponse
        });
    }

    e.preventDefault();

    return false;
});

creditTransactions = $('#credit-container');
prototypeCreditTransactions = $('#credit-container').find("tbody").data('prototype');
creditTransactions.data('index', creditTransactions.length);

debitTransactions = $('#debit-container');
prototypeDebitTransactions = $('#debit-container').find("tbody").data('prototype');
debitTransactions.data('index', debitTransactions.length);

$("#addcredit").click(function () {
    addCreditTransaction(creditTransactions);
});

$("#adddebit").click(function () {
    addDebitTransaction(debitTransactions);
});

function addCreditTransaction($collectionHolder) {
    var index = $collectionHolder.data('index');
    var newForm = prototypeCreditTransactions.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    $collectionHolder.find('#itens').prepend(newForm);
}

function addDebitTransaction($collectionHolder) {
    var index = $collectionHolder.data('index');
    var newForm = prototypeDebitTransactions.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    $collectionHolder.find('#itens').prepend(newForm);
}

new Jodit("#project_description", {
    buttons: ['bold', 'underline', 'italic', 'ol', 'ul']
});

$('.datepicker').datepicker();

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

if ($('#project-profit-bar-chart').length > 0) {
    $('#project-profit-bar-chart').sparkline([2, 5, 6, 10, 9, 12, 4, 9], {
        type: 'bar',
        height: '20',
        barWidth: '3',
        resize: true,
        barSpacing: '3',
        barColor: '#9675ce',
        label: ['q', 'q', 'q', 'q', 'q', 'q', 'q', 'q']
    });
}

var placesAutocomplete = places({
    appId: 'plZHZBKI3Z55',
    apiKey: '6328323e95ae06e2bb7a4795ee6f45b3',
    container: document.querySelector('#project_address_addressLine1'),
    templates: {
        value: function (suggestion) {
            return suggestion.name;
        }
    }
}).configure({
    type: 'address'
});
placesAutocomplete.on('change', function resultSelected(e) {
    document.querySelector('#project_address_addressLine2').value = e.suggestion.administrative || '';
    document.querySelector('#project_address_city').value = e.suggestion.city || '';
    document.querySelector('#project_address_postcode').value = e.suggestion.postcode || '';
    $("#project_address_country").val(e.suggestion.countryCode.toUpperCase());
    $("#project_address_lat").val(e.suggestion.latlng.lat);
    $("#project_address_lng").val(e.suggestion.latlng.lng);
});

function tabToAccordion(){
    var navTab = $(".nav-tabs"),
        tabContent = $(".tab-content");
        // hiding the navtabs
    navTab.hide();
    // appending each link to respective tab-pane
        navTab.find("li").each(function(){
            var destination = $($(this).find(".nav-link").attr("href"));
            var anchor = $(this).find(".nav-link");
            // removing unused attributes and adding tabContent-toggler class
            anchor.removeAttr("data-toggle role aria-controls aria-selected").addClass("card-header tabContent-toggler").insertBefore(destination);
        });
    var tabToggler = $('.tabContent-toggler'),
    tabPane = tabContent.find(".tab-pane"),
    // get all classes in tab pane for further usage and replace tab-pane with empty data
    nonActiveTabPane = tabContent.find(".tab-pane:not(.active)").attr("class");
    var tabPaneClass = tabPane.attr('class');
    tabPaneClass = tabPaneClass.replace(nonActiveTabPane,"");
    tabToggler.click(function(e){
        // get the destination of clicked element
        var destination = $($(this).attr("href"));
        // if not this element then remove active class
        $(this).parent().find(tabToggler).not($(this)).removeClass("active");
        //if not clicked destination then remove all other class except tab-pane
        $(this).parent().find('.tab-pane').removeClass(tabPaneClass);
        // now toggle active class
        $(this).toggleClass("active");
        // also toggle all other class in tab-pane
        destination.toggleClass(tabPaneClass);
        // if this element dont have active class then remove all other class from tab-pane
        if(!$(this).hasClass("active")){
            destination.removeClass(tabPaneClass);
        }
        // first element of nested tab-pane should be active
        if(destination.has(tabToggler)){
            var tabTogglerFirstChild = destination.find(".tabContent-toggler:first-child"),
            tabTogglerFirstDestination = $(tabTogglerFirstChild.attr("href"));
            tabTogglerFirstChild.addClass("active");
            tabTogglerFirstDestination.addClass(tabPaneClass);
        }
        // preventing default behaviour of element
        e.preventDefault();
    });
}
// check if device is mobile and if so only run the function
if(/Mobi/.test(navigator.userAgent)){
    tabToAccordion();
}

import 'jodit/build/jodit.min.css'
import 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'
import { cropImageAndUpload } from '../../../utils/images'
import { uploadDocument } from '../../../utils/documents'
import { Jodit } from 'jodit';
import places from 'places.js'

(function () {
    'use strict'

    $('#btn-delete').click(function (e) {
        e.preventDefault();
        $('#remove-modal').modal('show');
        // $("#form-delete").submit();
        // return false;
    });

    $("#form-delete").on("submit", function () {
        if (confirm(MSG_DELETE_ITEM)) {
            return true;
        } else {
            $('form-delete').find('[type=submit],submit').prop('disabled', false);
            return false;
        }
    });

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

    $("[data-placement=right]").hover(function () {
        $('.tooltip').css('left', parseInt($('.tooltip').css('left')) + 100 + 'px')
    });

    new Jodit("#property_description", {
        buttons: ['bold', 'underline', 'italic', 'ol', 'ul']
    });

    $('.datepicker').datepicker();

    places({
        appId: 'plZHZBKI3Z55',
        apiKey: '6328323e95ae06e2bb7a4795ee6f45b3',
        container: document.querySelector('#property_address_addressLine1'),
        templates: {
            value: function (suggestion) {
                return suggestion.name;
            }
        }
    })
    .configure({
        type: 'address'
    })
    .on('change', function resultSelected(e) {
        document.querySelector('#property_address_addressLine2').value = e.suggestion.administrative || '';
        document.querySelector('#property_address_city').value = e.suggestion.city || '';
        document.querySelector('#property_address_postcode').value = e.suggestion.postcode || '';
        $("#property_address_country").val(e.suggestion.countryCode.toUpperCase());
        $("#property_address_lat").val(e.suggestion.latlng.lat);
        $("#property_address_lng").val(e.suggestion.latlng.lng);
    });
})();
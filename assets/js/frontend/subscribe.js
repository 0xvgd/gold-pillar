import places from 'places.js'

export default (function () {
    'use strict'


  var placesAutocomplete = places({
    appId: 'plZHZBKI3Z55',
    apiKey: '6328323e95ae06e2bb7a4795ee6f45b3',
    container: document.querySelector('#registration_address_addressLine1'),
    templates: {
      value: function(suggestion) {
        return suggestion.name;
      }
    }
  }).configure({
    type: 'address'
  });
  placesAutocomplete.on('change', function resultSelected(e) {
      debugger;
    document.querySelector('#registration_address_addressLine2').value = e.suggestion.administrative || '';
    document.querySelector('#registration_address_city').value = e.suggestion.city || '';
    document.querySelector('#registration_address_postcode').value = e.suggestion.postcode || '';
    $("#registration_address_country").val(e.suggestion.countryCode.toUpperCase());
  });

})();

$('.ckb-principal').on('change', function () {
    $('.ckb-principal').not(this).prop('checked', false);
});
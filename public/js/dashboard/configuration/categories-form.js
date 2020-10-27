(function () {
    'use strict'

    var tableValues;
    var prototipoTableValues;

    tableValues = $('#tab-values').children().children();
    prototipoTableValues = $('#tab-values').children().data('prototype');
    tableValues.data('index', tableValues.length);

    $("#addtablevalue").click(function () {
        addTableValue(tableValues);
    });

    function addTableValue($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototipoTableValues.replace(/__name__/g, index);

        tableValues.data('index', index + 1);

        $('#tab-values').find('#itens').append(newForm);
    }
})();

function removeTableValue(tableValueContainer) {
    tableValueContainer.parent().parent().remove();
}
(function () {
    'use strict'

    var roles;
    var prototypeRoles;

    var features;
    var prototypeFeatures;

    var units;
    var prototypeUnits;

    roles = $('#tab-roles').children().children();
    prototypeRoles = $('#tab-roles').children().data('prototype');
    roles.data('index', roles.length);

    features = $('#tab-features').children().children();
    prototypeFeatures = $('#tab-features').children().data('prototype');
    features.data('index', features.length);

    units = $('#tab-units').children().children();
    prototypeUnits = $('#tab-units').children().data('prototype');
    units.data('index', features.length);


    $("#addrole").click(function () {
        addRole(roles);
    });


    $("#addfeature").click(function () {
        addFeature(features);
    });

    $("#addunit").click(function () {
        addUnit(units);
    });

    $('body').on('focus', ".datepicker", function () {
        $(this).datepicker();
    });


    function addRole($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototypeRoles.replace(/__name__/g, index);

        roles.data('index', index + 1);

        $('#tab-roles').find('#itens').append(newForm);

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy'
        });

        $(".switch").bootstrapSwitch();
        $("select.select2-on").select2({
            placeholder: "Select...",
            allowClear: true,
            width: "100%"
        });
    }

    function addFeature($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototypeFeatures.replace(/__name__/g, index);

        features.data('index', index + 1);

        $('#tab-features').find('#itens').append(newForm);

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR'
        });

        $(".switch").bootstrapSwitch();
        $("select.select2-on").select2({
            placeholder: "Select...",
            allowClear: true,
            width: "100%"
        });
    }

    function addUnit($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototypeUnits.replace(/__name__/g, index);

        units.data('index', index + 1);

        $('#tab-units').find('#itens').append(newForm);

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR'
        });

        $(".switch").bootstrapSwitch();
        $("select.select2-on").select2({
            placeholder: "Select...",
            allowClear: true,
            width: "100%"
        });
    }

    $(".switch").bootstrapSwitch();
    $("select.select2-on").select2({
        placeholder: "Select...",
        allowClear: true,
        width: "100%"
    });

})();

function removeRole(roleContainer) {
    roleContainer.parent().parent().remove();
}

function removeFeature(featureContainer) {
    featureContainer.parent().parent().remove();
}

function removeUnit(unitContainer) {
    unitContainer.parent().parent().remove();
}


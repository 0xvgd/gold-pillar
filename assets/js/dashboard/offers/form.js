import '../../../css/offers.scss';
import 'jquery-mask-plugin';



$('#accept').on('click', function (e) {
    var btn = $(this);
    if (!btn.data('ok')) {
        e.preventDefault();
        swal.fire({
            title: "Atention",
            text: "Do you really want to accept this offer?",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "No",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.value) {
                $('#acceptForm').attr('action', btn.data('action')).submit();
            }
        });
    }
});

$('#decline').on('click', function (e) {
    var btn = $(this);
    if (!btn.data('ok')) {
        e.preventDefault();
        swal.fire({
            title: "Atention",
            text: "Do you really want to decline this offer?",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "No",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.value) {
                btn.data('ok', 1);
                 $('#declineForm').attr('action', btn.data('action')).submit();
            }
        });
    }
});

 $('#counterOffer').on('click', function (e) {
        var btn = $(this);
        if (!btn.data('ok')) {
            e.preventDefault();
            Swal.fire({
                title: "Counter offer",
                input: 'text',
                inputPlaceholder: 'Counter offer value',
                inputValidator: (value) => {
                    if (!value) {
                    return 'Insert a new offer amount'
                    }
                },
                text: "New offer amount",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save',
                cancelButtonText: "Cancel",
                onOpen: function () {
                    $('.swal2-input').focus()
                    $('.swal2-input').mask("#,##0.00", {reverse: true});
                },
                }).then((result) => {
                if (result.value) {
                   $('#counterOfferValue').val(result.value);
                   
                   $('#counterOfferForm').attr('action', btn.data('action')).submit();
                }
            })
        }
    });
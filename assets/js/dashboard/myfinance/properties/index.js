import moment from '../../../utils/moment'

const datatableCommissionPayment = $('#datatable-commission-payment')
const baseUrl = datatableCommissionPayment.data('url')

datatableCommissionPayment.dataTable({
    ajax: `${baseUrl}search_commission.json`,
    processing: true,
    serverSide: true,
    searching: false,
    columns: [
        {
            data: 'createdAt',
            render(date) {
                return moment(date).format('L')
            }
        },
        {
            data: 'transaction',
            render(transaction) {
                return [
                    transaction.transactionId,
                    (transaction.verified ? 
                        '<i class="fa fa-check text-success" title="Transaction security verified"></i>'
                    :
                        '<i class="fa fa-exclamation-circle text-danger" title="Transaction security NOT verified"></i>'
                    )
                ].join(' ')
            }
        },
        {
            data: 'transaction.amount',
            render(amount) {
                const formatter = new Intl.NumberFormat('uk', {
                    style: 'currency',
                    currency: 'GBP',
                })
                return formatter.format(amount);
            }
        },
        {
            data: 'transaction.status',
            render(status) {
                if (status.value === 'processed') {
                    return `<span class="badge badge-success">Processed</span>`
                }
                if (status.value === 'created') {
                    return `<span class="badge badge-warning">Created</span>`
                }
                return `<span class="badge badge-danger">${status.label}</span>`
            }
        },
        {
            data: 'resource.name'
        }
    ]
})
.on('preXhr.dt', function (e, settings, data) {
    $('#search-form').each(function (i, e) {
        data.search = $(e).serialize();
    });
})
.on('xhr.dt', function (e, settings, data) {
    $('[type=submit]').prop('disabled', false);
});
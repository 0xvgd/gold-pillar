import moment from '../../../utils/moment'

const datatable = $('#datatable')
const baseUrl = datatable.data('url')
const $form = $('#search-form')

datatable.dataTable({
    ajax: `${baseUrl}search.json`,
    processing: true,
    ordering: true,
    columnDefs: [
        {
            orderable: false,
            targets: "no-sort"
        }
    ],
    serverSide: true,
    searching: false,
    columns: [
        {
            data: 'createdAt',
            render(date) {
                return moment(date).format('DD/MM/YYYY h:mm:ss')
            }
        },
        {
            data: 'referenceCode',
        },
        {
            data: 'fee',
            render(fee) {
                var formatter = new Intl.NumberFormat('uk', {
                    style: 'currency',
                    currency: fee.currency
                });
                return formatter.format(fee.amount);

            }
        },
        {
            data: 'accommodation.name',
        },
        {
            data: 'peopleCount',
        },
        {
            data: 'processedAt',
            className: 'text-center',
            render(processedAt) {
                return processedAt ? '<i class="fas fa-check text-success"></i>' : ' <i class="fas fa-clock text-warning"></i>';
            }
        },
        {
            data: 'id',
            className: 'text-right',
            render(id) {
                return `<a href="${baseUrl}${id}" class="btn btn-default" title="View"><span class="fas fa-eye"></span></a>`;
            }
        }
    ]
})
.on('preXhr.dt', function (e, settings, data) {
    data.search = $form.serialize()
})
.on('xhr.dt', function (e, settings, data) {
    $('[type=submit]').prop('disabled', false)
})

$form.on('submit', () => {
    datatable.DataTable().ajax.reload()
    return false;
})
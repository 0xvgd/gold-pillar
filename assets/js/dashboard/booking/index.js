import moment from '../../utils/moment'

const datatable = $('#datatable')
const baseUrl = datatable.data('url')

datatable.dataTable({
    ajax: `${baseUrl}search.json`,
    processing: true,
    serverSide: true,
    searching: false,
    columns: [
        {
            data: 'event.date',
            render(date) {
                return moment(date).utc(false).format('DD/MM/YYYY')
            }
        },
        {
            data: 'event.start',
            render(date) {
                return moment(date).utc(false).format('HH:mm')
            }
        },
        {
            data: 'event.end',
            render(date) {
                return moment(date).utc(false).format('HH:mm')
            }
        },
        {
            data: 'resource.name'
        },
        {
            data: 'agent.user.name'
        },
        {
            data: 'customer.name'
        },
        {
            data: 'id',
            className: 'text-right',
            render(id) {
                return `<a href="${baseUrl}${id}" class="btn btn-default" title="View"><span class="fas fa-eye"></span></a>`
            }
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

$('#search-form').on('submit', function () {
    datatable.DataTable().ajax.reload();
    return false;
});
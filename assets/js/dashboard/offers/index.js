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
            data: 'createdAt',
            render(date) {
                return moment(date).format('DD/MM/YYYY h:mm:ss')
            }
        },
        {
            data: 'tenant.user',
            render: function(user,type, row) {
                return user.name +"<br>" + user.email;
            }
        },
        {
            data: 'resource',
            render: function(resource) {
                 var path = ["/renting/", resource.slug, "/view"].join("");
                return "<a href='"+ path +"' target='_blank'>"+ resource.name +"</a>"
            }
        },
        {
            data: 'originalPrice',
            render: function (originalPrice, type, row) {
                return formatter.format(row["originalPrice.amount"]);
            }
        },
        {
            data: 'offerValue',
            render: function (offerValue) {
                return formatter.format(offerValue);
            }
        },
        {
            data: 'status.label'
        },
        {
            data: 'id',
            className: 'text-right',
            render: function (id) {
                return `<a href="${baseUrl}edit/${id}" class="btn btn-default" title="Edit"><span class="fas fa-eye"></span></a>`;
            }
        }
    ]
})
.on('preXhr.dt', function (e, settings, data) {
    $('#search-form').each(function (i, e) {
        var form = $(e),
                type = form.data('type');
        data.search[type] = form.serialize();
    });
})
.on('xhr.dt', function (e, settings, data) {
    $('[type=submit]').prop('disabled', false);
});

$('#search-form').on('submit', function () {
    datatable.DataTable().ajax.reload();

    return false;
});

$('#search-form').on('submit', () => {
    datatable.DataTable().ajax.reload();
    return false;
});
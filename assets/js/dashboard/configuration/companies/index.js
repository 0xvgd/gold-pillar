const datatable = $('#datatable')
const baseUrl = datatable.data('url')

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
            data: 'name'
        },
        {
            data: 'defaultCompany'
        },
        {
            data: 'id',
            className: 'text-right',
            render(id) {
                return  `<a href="${baseUrl}${id}" class="btn btn-default" title="Edit"><span class="fas fa-pen"></span></a>`
            }
        }
    ]
})
.on('preXhr.dt', (e, settings, data) => {
    data.search = $('#search-form').serialize();
})
.on('xhr.dt', (e, settings, data) => {
    $('[type=submit]').prop('disabled', false);
});

$('#search-form').on('submit', () => {
    datatable.DataTable().ajax.reload();
    return false;
});
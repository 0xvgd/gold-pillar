const datatable = $("#datatable");
const baseUrl = datatable.data("url");

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
			data: 'mainPhoto',
			render(data, type, row) {
				return `<a class="#" href="${data}"><img src="${data}/thumb:30*30*outbound" ></a>`;
			}
		},
		{
			data: "name"
		},
		{
			data: "purchasePrice",
			className: "col-hidden",
			render(data) {
				var formatter = new Intl.NumberFormat("uk", {
					style: "currency",
					currency: "GBP"
				});
	
				return formatter.format(data.amount);
			}
		},
		{
			data: "salePriceProjection",
			className: "col-hidden",
			render(data) {
				var formatter = new Intl.NumberFormat("uk", {
					style: "currency",
					currency: "GBP"
				});
	
				return formatter.format(data.amount);
			}
		},
		{
			data: "hits",
			className: "col-hidden"
		},
		{
			data: "postStatus",
			render(data) {
				let span = "";
				if (data && data.value) {
					let estilo = "";
					switch (data.value) {
						case "approved":
							estilo = " fa fa-check fa-2x c-green-500";
							break;
						case "denied":
							estilo = " fas fa-exclamation-triangle fa-2x c-red-500";
							break;
						case "on_approval":
							estilo = " fas fa-clock fa-2x c-orange-300";
							break;
					}
					span = `<span class="${estilo}" title="${data.label}"></span>`;
				}
				return span;
			}
		},
		{
			data: "projectStatus",
			render(data) {
				let span = "";
				if (data && data.value) {
					let estilo = "";
					switch (data.value) {
						case "for_sale":
							estilo = "c-blue-500";
							break;
						case "sold":
							estilo = "c-green-500";
							break;
						case "removed":
							estilo = "c-red-500";
							break;
					}
					span = `<span class="${estilo}" title="${data.label}"><b>${data.label}</b></span>`;
				}
				return span;
			}
		},
		{
			data: "id",
			className: "text-right",
			render(id) {
				return [
					`<a href="${baseUrl}${id}/finance" class="btn btn-default" title="Financial info"><span class="fas fa-calculator"></span></a>`,
					`<a href="${baseUrl}${id}" class="btn btn-default" title="Edit"><span class="fas fa-pen"></span></a>`,
				].join(' ')
			}
		}
	]
})
.on("preXhr.dt", (e, settings, data) => {
	data.search = $("#search-form").serialize();
})
.on("xhr.dt", (e, settings, data) => {
	$("[type=submit]").prop("disabled", false);
});

$("#search-form").on("submit", () => {
	datatable.DataTable().ajax.reload();
	return false;
});

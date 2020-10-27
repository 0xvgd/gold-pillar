import moment from "moment";

const datatable = $("#datatable");
const baseUrl = datatable.data("url");
const isAdmin = datatable.data("isAdmin");

let columns = [
  {
    data: "mainPhoto",
    render: function(data, type, row) {
      return `<a class="#" href="${data}"><img src="${data}/thumb:30*30*outbound" ></a>`;
    }
  },
  {
    data: "name"
  },
  {
    data: "rent",
    className: "col-hidden",
    render: function(data) {
      if (data) {
        var formatter = new Intl.NumberFormat("uk", {
          style: "currency",
          currency: data.currency
        });
      }
      return data ? formatter.format(data.amount) : "--";
    }
  },
  {
    data: "hits",
    className: "col-hidden"
  }
];

if (isAdmin) {
  columns.push({
    data: "owner",
    render: function(data) {
      return data ? data.name : "--";
    }
  });
}
columns = columns.concat([
  {
    data: "postStatus",
    className: "col-hidden",
    render: function(data, type, row) {
      var estilo =
        data.label === "Approved"
          ? " fa fa-check fa-2x c-green-500"
          : data.label === "Denied (contact us)"
          ? " fas fa-exclamation-triangle fa-2x c-red-500"
          : data.label === "On Approval"
          ? " fas fa-clock fa-2x c-orange-300"
          : data.label === "Sold"
          ? " fas fa-shopping-cart fa-2x c-blue-500"
          : "";
      return '<span class="' + estilo + '" title="' + data.label + '"></span>';
    }
  },
  {
    data: "status",
    className: "col-hidden",
    render(data, type, row) {
      var span = "";
      if (data && data.value) {
        var estilo = "";
        switch (data.value) {
          case "to_rent":
            estilo = "c-blue-500";
            break;
          case "reserved":
            estilo = "c-green-500";
            break;
          case "rented":
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
      return `<a href="${baseUrl}${id}" class="btn btn-default" title="Edit"><span class="fas fa-pen"></span></a>`;
    }
  }
]);

datatable
  .dataTable({
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
    columns: columns
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

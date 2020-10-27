
import Vue from 'vue'
import OfferItem from '../../components/OfferItem.vue'
import Vue2Filters from 'vue2-filters'

Vue.use(Vue2Filters)

new Vue({
    el: '#app',
    components: {
        'offer-item': OfferItem
    },
    data: {
        baseUrl: GpApp.baseUrl,
        items: [],
        errorMessage: null,
        loading: false,
        searching: false,
        loadingTime: 500,
        errorClass: null,
        total: 0,
        page: 0,
        pageLength: 50,
        advancedFilter:false
    },
    methods: {
        reset: function () {
            this.errorMessage = null;
            this.errorClass = null;
        },
        resetItems: function () {
            this.reset();
            this.items = [];
            this.total = 0;
            this.page = 0;
        },
        search: function (clear) {
            if (clear) {
                this.resetItems();
            } else {
                this.reset();
            }
            this.showItems(this.page);
        },
        showItems: function (page) {
            var self = this;
            self.searching = true;
            setTimeout(function () {
                if (self.searching) {
                    self.loading = true;
                }
            }, self.loadingTime);
            this.getOffers(page).then(function (response) {
                self.total = response.recordsTotal;
                self.page = page;
                self.searching = false;
                self.loading = false;
                if (response.error) {
                    self.errorClass = 'danger';
                    self.errorMessage = response.error;
                } else {
                    self.items = response.data;
                    if (self.items.length === 0) {
                        self.errorClass = 'warning';
                        self.errorMessage = 'No items found with selected filters.';
                    } else {
                        self.page++;
                    }
                }
            });
        },
        goto: function (page) {
            if (page < 0 || page * this.pageLength > this.total) {
                return;
            }
            this.showItems(page);
        },
        getOffers: function (page, args) {
            debugger;
            args = args || {};
            args.length = this.pageLength;
            args.start = page * this.pageLength;
            return $.ajax({
                url: GpApp.baseUrl + 'dashboard/tenant/search' + '?' + $.param(args),
                type: 'POST',
                data: {
                    form: $('#search-form').serialize()
                },
                complete: function () {

                }
            });
        }
    },
    beforeMount() {
        this.search()
    },
    mounted() {
        $('.btn-decline').on('click', function (e) {
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
                        btn.trigger('click');
                    }
                });
            }
        }); 
    }
})

$(function() {
    $('.btn-decline').on('click', function (e) {
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
                    btn.trigger('click');
                }
            });
        }
    });
});


import Vue from 'vue'
import AssetItem from '../../components/AssetItem.vue'
import Vue2Filters from 'vue2-filters'

function slug(str) {
    return str.trim()
            .replace(/([a-z])([A-Z])/g, '$1-$2')
            .replace(/\W/g, function (m) {
                return /[À-ž]/.test(m) ? m : '-'
            })
            .replace(/^-+|-+$/g, '')
            .replace(/-{2,}/g, function (m) {
                return '-'
            })
            .toLowerCase();
}

Vue.use(Vue2Filters)

new Vue({
    el: '#app',
    components: {
        'asset-item': AssetItem
    },
    data: {
        baseUrl: GpApp.baseUrl,
        assets: [],
        errorMessage: null,
        loading: false,
        searching: false,
        loadingTime: 500,
        assetErrorMessage: null,
        errorClass: null,
        total: 0,
        page: 0,
        pageLength: 50
    },
    methods: {
        reset: function () {
            this.errorMessage = null;
            this.errorClass = null;
            this.assetErrorMessage = null;
        },
        resetAssets: function () {
            this.reset();
            this.assets = [];
            this.total = 0;
            this.page = 0;
        },
        search: function (clear) {
            if (clear) {
                this.resetAssets();
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
            this.assetsSearch(page).then(function (response) {
                self.total = response.recordsTotal;
                self.page = page;
                self.searching = false;
                self.loading = false;
                if (response.error) {
                    self.errorClass = 'danger';
                    self.assetErrorMessage = response.error;
                } else {
                    self.assets = response.data;
                    if (self.assets.length === 0) {
                        self.errorClass = 'warning';
                        self.assetErrorMessage = 'No items found with selected filters.';
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
        assetsSearch: function (page, args) {
            var self = this,
                    args = args || {};
            args.length = this.pageLength;
            args.start = page * this.pageLength;
            return $.ajax({
                url: GpApp.baseUrl + 'dashboard/investor/assets/search.json' + '?' + $.param(args),
                type: 'POST',
                data: {
                    formulario: null
                },
                complete: function () {

                }
            });
        }
    },
    beforeMount() {
        this.search();
    }
})
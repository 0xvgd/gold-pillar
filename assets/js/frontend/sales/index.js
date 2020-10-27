import '../../../css/sales.scss'
import Vue from 'vue'
import PropertyItem from '../../components/PropertyItem.vue'
import Vue2Filters from 'vue2-filters'
import transFilter from 'vue-trans';
Vue.use(transFilter)
Vue.use(Vue2Filters)

const request = async (args) => {
    return new Promise((resolve, reject) => {
        args = args || {};
        $.ajax({
            url: GpApp.baseUrl + 'sales/search.json?' + $.param(args),
            type: 'POST',
            data: {
                form: $('#search-form').serialize()
            },
            success(response) {
                resolve(response)
            },
            error(error) {
                reject(error)
            }
        });
    })
}

new Vue({
    el: '#app',
    components: {
        PropertyItem
    },
    data: {
        loading: false,
        properties: [],
        errorMessage: null,
        propertyErrorMessage: null,
        errorClass: null,
        total: 0,
        page: 0,
        pageLength: 50,
        advancedFilter: false,
        placeholder: {
            tag: '-',
            name: '-',
            propertyType: {
                label: '',
            },
            propertyStatus: {
                value: '',
            },
        }
    },
    methods: {
        reset() {
            this.errorMessage = null;
            this.errorClass = null;
            this.propertyErrorMessage = null;
        },
        resetProperties() {
            this.reset();
            this.properties = [];
            this.total = 0;
            this.page = 0;
        },
        search(clear) {
            if (clear) {
                this.resetProperties();
            } else {
                this.reset();
            }
            this.fetch(this.page);
        },
        async fetch(page) {
            if (this.loading) {
                return;
            }
            this.loading = true;
            try {
                const response = await request({
                    length: this.pageLength,
                    start: page * this.pageLength,
                })
                this.total = response.recordsTotal;
                this.page = page;
                if (response.error) {
                    this.errorClass = 'danger';
                    this.propertyErrorMessage = response.error;
                } else {
                    this.properties = response.data;
                    if (this.properties.length === 0) {
                        this.errorClass = 'warning';
                        this.propertyErrorMessage = 'No items found with selected filters.';
                    } else {
                        this.page++;
                    }
                }
            } catch (e) {
                console.error(e)
                this.errorClass = 'danger';
                this.propertyErrorMessage = e.message;
            }
            this.loading = false
        },
        goto(page) {
            if (page < 0 || page * this.pageLength > this.total) {
                return;
            }
            this.fetch(page);
        },
    },
    beforeMount() {
        this.search()
    }
})

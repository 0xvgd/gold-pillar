import '../../../css/assets.scss'
import Vue from 'vue'
import AssetItem from '../../components/AssetItem.vue'
import Vue2Filters from 'vue2-filters'
import transFilter from 'vue-trans';
Vue.use(transFilter)
Vue.use(Vue2Filters)

const request = async (args) => {
    return new Promise((resolve, reject) => {
        args = args || {};
        $.ajax({
            url: GpApp.baseUrl + 'assets/search.json?' + $.param(args),
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
        AssetItem,
    },
    data: {
        loading: false,
        assets: [],
        errorMessage: null,
        assetErrorMessage: null,
        errorClass: null,
        total: 0,
        page: 0,
        pageLength: 50,
        minValueInput: minValueInput,
        maxValueInput: maxValueInput,
        minValue:50,
        maxValue:500,
        name: null,
        advancedFilter: false,
        placeholder: {
            tag: '-',
            name: '-',
            assetType: {
                label: '',
            },
        }
    },
    methods: {
        valueChangedByInput() {
            //debugger;
            //this.$refs.minValueSlider.sliderValue = this.minValueInput;
            //this.$refs.minValueSlider.valueChanged();
        },
        maxValueChangedByInput() {
           // debugger;
           // this.$refs.maxValueSlider.sliderValue = this.maxValueInput;
           // this.$refs.maxValueSlider.valueChanged();
        },
        reset() {
            this.errorMessage = null;
            this.errorClass = null;
            this.assetErrorMessage = null;
        },
        resetAssets() {
            this.reset();
            this.assets = [];
            this.total = 0;
            this.page = 0;
        },
        search(clear) {
            if (clear) {
                this.resetAssets();
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
                    this.assets = response.data;
                    if (this.assets.length === 0) {
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
        goto: function (page) {
            if (page < 0 || page * this.pageLength > this.total) {
                return;
            }
            this.fetch(page);
        },
    },
    beforeMount() {
        this.search();
        this.valueChangedByInput();
        this.maxValueChangedByInput();
    }
})
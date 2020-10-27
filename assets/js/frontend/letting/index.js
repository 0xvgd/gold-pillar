import Vue from 'vue'
import moment from '../../utils/moment'
import { CurrencyInput } from 'vue-currency-input'
import ReviewStars from '../../components/ReviewStars.vue'
import transFilter from 'vue-trans';
Vue.use(transFilter)

Vue.filter('relativeTime', (dt) => {
    return moment(dt).fromNow(true)
})

Vue.filter('currency', (value) => {
    const formatter = new Intl.NumberFormat('GBP', {
        style: 'currency',
        currency: 'GBP'
    });
    return formatter.format(value)
})

new Vue({
    el: '#app',
    components: {
        CurrencyInput,
        ReviewStars,
    },
    data() {
        return {
            rentValue: 0,
            findTenantFee: 0,
            findOnlyCharge: 0,
            rentCollectCharge: 0,
            fullyManagedCharge: 0,
            agent: null,
            agents: [],
            plan: '',
            loading: false,
            showAgents: false,
            showAgent: false,
            authenticated: false,
        }
    },
    computed: {
        yearly() {
            return this.rentValue * 12
        },
        findOnlyFee() {
            return this.yearly * this.findOnlyCharge + this.findTenantFee
        },
        rentCollectFee() {
            return this.rentValue * this.rentCollectCharge
        },
        fullyManagedFee() {
            return this.rentValue * this.fullyManagedCharge
        },
    },
    methods: {
        choosePlan(plan) {
            this.plan = plan
        },
        fetchAgents() {
            this.showAgent = false
            this.showAgents = false
            this.loading = true
            $.ajax({
                url: this.$el.dataset.agentsUrl,
                success: (response) => {
                    this.agents = response
                    this.showAgents = true
                },
                complete: () => {
                    this.loading = false
                    setTimeout(() => {
                        window.scrollTo(0, $(this.$refs.agents).offset().top - 160)
                    }, 400)
                }
            })
        },
        chooseAgent(agent) {
            this.showAgents = false
            this.showAgent = true
            this.agent = agent
            setTimeout(() => {
                window.scrollTo(0, $(this.$refs.agent).offset().top - 160)
                this.fetchDates()
            }, 400)
        },
        fetchDates() {
            const $select = $(this.$refs.booking_date)
            $select.html('<option value=""></option>')
            if (this.agent) {
                $.ajax({
                    url: `/booking/agents/${this.agent.id}/days`,
                    success: (response) => {
                        for (let date of response) {
                            let str = moment(date, "YYYY-MM-DD").format('L')
                            $select.append(`<option value="${date}">${str}</option>`)
                        }
                    }
                })
            }
        },
        fetchTimes(date) {
            const $select = $(this.$refs.booking_time)
            $select.html('<option value=""></option>')
            if (date) {
                $.ajax({
                    url: `/booking/agents/${this.agent.id}/days/${date}`,
                    success: (response) => {
                        for (let time of response) {
                            $select.append(`<option value="${time}">${time}</option>`)
                        }
                    }
                })
            }
        },
        changeDate(e) {
            const date = e.currentTarget.value
            this.fetchTimes(date)
        },
    },
    mounted() {
        this.plan = this.$el.dataset.plan
        this.rentValue = parseFloat(this.$el.dataset.rentValue)
        this.findTenantFee = parseFloat(this.$el.dataset.findTenantFee)
        this.findOnlyCharge = parseFloat(this.$el.dataset.findOnlyCharge)
        this.rentCollectCharge = parseFloat(this.$el.dataset.rentCollectCharge)
        this.fullyManagedCharge = parseFloat(this.$el.dataset.fullyManagedCharge)
        this.authenticated = this.$el.dataset.authenticated === '1'

        if (this.$el.dataset.agent) {
            $.ajax({
                url: this.$el.dataset.agentsUrl + '/' + this.$el.dataset.agent,
                success: (response) => {
                    this.agent = response
                    this.showAgent = true
                    this.showAgents = false
                }
            })
        }

        // override default behavior (window.reload)
        $('#sign-in-modal form')
            .off('sign-in-success')
            .on('sign-in-success', () => {
                this.authenticated = true
                $('#sign-in-modal').modal('hide')
            })
    }
})

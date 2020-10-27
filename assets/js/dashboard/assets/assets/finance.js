import axios from 'axios'
import moment from '../../../utils/moment'
import BalanceValue from '../../../components/finance/BalanceValue.vue'
import RecurringForm from '../../../components/finance/RecurringForm.vue'
import RecurringList from '../../../components/finance/RecurringList.vue'
import PaymentForm from '../../../components/finance/PaymentForm.vue'
import PaymentList from '../../../components/finance/PaymentList.vue'
import InvestmentList from '../../../components/finance/InvestmentList.vue'
import DividendList from '../../../components/finance/DividendList.vue'
import TransactionList from '../../../components/finance/TransactionList.vue'

Vue.filter('date', (dt) => {
    return moment(dt).format('ll')
})

Vue.filter('dateTime', (dt) => {
    return moment(dt).format('lll')
})

Vue.filter('time', (dt) => {
    return moment(dt).format('LT')
})

Vue.filter('numberFormat', (value, fraction=2) => {
    const formatter = new Intl.NumberFormat('uk', {
        minimumFractionDigits: fraction,
        maximumFractionDigits: fraction,
        useGrouping: true,
    });
    return formatter.format(value)
})

Vue.filter('currency', (value, currency='GBP') => {
    const formatter = new Intl.NumberFormat('uk', {
        style: 'currency',
        currency,
    });
    return formatter.format(value)
})

new Vue({
    el: '#app',
    components: {
        BalanceValue,
        RecurringForm,
        RecurringList,
        PaymentForm,
        PaymentList,
        InvestmentList,
        DividendList,
        TransactionList,
    },
    data() {
        return {
            balanceLoading: true,
            baseUrl: '',
            balance: 0,
            totalInvested: 0,
        }
    },
    computed: {
        dividendBalance() {
            return Math.max(0, this.balance - this.totalInvested)
        }
    },
    methods: {
        async updateBalance() {
            this.balanceLoading = true
            let url = await `${this.baseUrl}/account`
            const response = await axios.get(url)
            const account = response.data
            this.balance = account.balance * 1.0
            this.balanceLoading = false
        },
        onAddPayment(modal, refName) {
            this.closeAndReload(modal, refName)
            this.updateBalance()
            this.$refs.transactionList.fetch()
        },
        openModal(selector) {
            $(selector).modal('show')
        },
        closeModal(selector) {
            $(selector).modal('hide')
        },
        reload(refName) {
            const component = this.$refs[refName]
            if (component) {
                component.fetch()
            }
        },
        reset(refName) {
            const component = this.$refs[refName]
            if (component) {
                component.reset()
            }
        },
        closeAndReload(modal, refName) {
            this.closeModal(modal)
            this.reload(refName)
        },
        resetAndOpenModal(modal, refName) {
            this.reset(refName)
            this.openModal(modal)
        },
        editRecurring(modal, form, entity) {
            const component = this.$refs[form]
            if (component) {
                this.openModal(modal)
                component.edit(entity.id)
            }
        },
        handleSubmit(event, refName) {
            event.preventDefault()
            try {
                const component = this.$refs[refName]
                if (component) {
                    component.submit()
                }
            } catch (e) {
                console.error(e)
            }
        }
    },
    beforeMount() {
        this.baseUrl = this.$el.dataset.baseUrl
        this.totalInvested = this.$el.dataset.totalInvested
        this.updateBalance()
    }
})


const checkValue = ($elem) => {
    const $parent = $elem.parents('.input-group:first')
    const $selected = $elem.find('option:selected')
    const unit = $selected.data('unit')
    $parent.find('.recurring-interval-value').hide()
    if (unit) {
        $parent.find(`.recurring-interval-value.${unit}`).show()
    }
}

$('.recurring-interval').each((i, e) => {
    const $elem = $(e)

    $elem.on('change', () => {
        checkValue($elem)
    })

    checkValue($elem)

    $elem.removeClass('recurring-interval')
});
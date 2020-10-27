<template>
    <span :class="balanceColor">
        <span v-if="!loading">
            {{ currency }}{{ amount|numberFormat(2) }}
        </span>
        <span class="spinner-border spinner-border-sm" role="status" v-if="loading">
            <span class="sr-only">Loading...</span>
        </span>
    </span>
</template>

<script>
import Vue from 'vue'

export default Vue.extend({
    props: {
        loading: {
            type: Boolean,
            required: true,
        },
        currency: {
            type: String,
            required: true,
        },
        amount: {
            type: Number,
            required: true,
        }
    },
    computed: {
        balanceColor() {
            if (this.amount > 0) {
                return 'text-success'
            } else if (this.amount < 0) {
                return 'text-danger'
            }
            return 'text-dark'
        }
    }
})
</script>
<template>
    <span>
        {{ transaction.transactionId }}
        <i class="fa fa-check text-success" title="Transaction security verified" v-if="transaction.verified"></i>
        <i class="fa fa-exclamation-circle text-danger" title="Transaction security NOT verified" v-else></i>
    </span>
</template>

<script>
import Vue from 'vue'

export default Vue.extend({
    props: {
        transaction: {
            type: Object,
            required: true,
        }
    }
})
</script>
<template>
    <fragment>
        <span class="badge badge-success" v-if="investment.status.value == 'approved'">Approved</span>
        <span class="badge badge-warning" v-else-if="investment.status.value == 'waiting'">Waiting</span>
        <span class="badge badge-danger" v-else>{{ investment.status.label }}</span>
    </fragment>
</template>

<script>
import Vue from 'vue'
import { Fragment } from 'vue-fragment'

export default Vue.extend({
    components: {
        Fragment,
    },
    props: {
        investment: {
            type: Object,
            required: true,
        }
    }
})
</script>
<template>
    <fragment>
        <span class="badge badge-success" v-if="transaction.status.value == 'processed'">Processed</span>
        <span class="badge badge-warning" v-else-if="transaction.status.value == 'created'">Created</span>
        <span class="badge badge-danger" v-else>{{ transaction.status.label }}</span>
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
        transaction: {
            type: Object,
            required: true,
        }
    }
})
</script>
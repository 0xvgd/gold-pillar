<template>
    <div :class="`alert alert-${color}`" v-if="message">
        {{ message }}
    </div>
</template>

<script>
import Vue from 'vue'

export default Vue.extend({
    props: {
        color: {
            type: String,
            required: true,
        },
        message: {
            type: String|null,
        }
    }
})
</script>
<template>
    <div :class="(valid ? 'valid' : 'invalid') + '-feedback'" style="display: block">
        {{ message }}
    </div>
</template>

<script>
import Vue from 'vue'

export default Vue.extend({
    props: {
        valid: {
            type: Boolean,
            required: true,
        },
        message: {
            type: String|null,
        }
    }
})
</script>
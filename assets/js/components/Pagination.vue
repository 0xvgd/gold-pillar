<template>
    <nav>
        <ul class="pagination">
            <li :class="'page-item ' + (!hasPrev && 'disabled')">
                <a class="page-link" href="#" @click.prevent="$emit('prev')">Previous</a>
            </li>
            <li :class="'page-item ' + (!hasNext && 'disabled')">
                <a class="page-link" href="#" @click.prevent="$emit('next')">Next</a>
            </li>
        </ul>
    </nav>
</template>

<script>
import Vue from 'vue'

export default Vue.extend({
    props: {
        hasPrev: {
            type: Boolean,
            required: true,
        },
        hasNext: {
            type: Boolean,
            required: true,
        },
    }
})
</script>
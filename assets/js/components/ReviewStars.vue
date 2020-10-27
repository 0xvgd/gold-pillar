<template>
    <span>
        <span v-for="i in starsRange" :key="i">
            <i class="fas fa-star" v-if="stars > i"></i>
            <i class="fas fa-star-half-alt" v-else-if="stars > i - 1"></i>
            <i class="far fa-star" v-else></i>
        </span>
    </span>
</template>

<script>
export default {
    props: {
        stars: {
            type: Number,
            required: true,
        },
        min: {
            type: Number,
            default: 1,
        },
        max: {
            type: Number,
            default: 5,
        },
    },
    computed: {
        calculatedStars() {
            let stars = Math.round(stars || 0)
            return stars
        },
        starsRange() {
            let range = []
            for (let i = this.min; i <= this.max; i++) {
                range.push(i)
            }
            return range
        }
    }
}
</script>
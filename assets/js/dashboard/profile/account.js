import Vue from 'vue'
import Avatar from '../../components/Avatar.vue'

Vue.use(Avatar)

new Vue({
    el: '#app',
    components: {
        'avatar': Avatar
    }
});
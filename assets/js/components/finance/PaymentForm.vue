<template>
    <div>
        <alert-message color="danger" :message="errors.message" />

        <div class="form-group">
            <label class="required">Note</label>
            <input
                type="text"
                required="required"
                :class="'form-control ' + (!!errors.fields['note'] && 'is-invalid')"
                v-model="entity.note" />
            <feedback :valid="false" :message="errors.fields['note']" />
        </div>
        <div class="form-group">
            <label class="required">Amount</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">€ </span>
                </div>
                <currency-input
                    required="required"
                    locale="en-gb"
                    :currency="null"
                    :class="'form-control ' + (!!errors.fields['amount'] && 'is-invalid')"
                    :auto-decimal-mode="true"
                    :allow-negative="false"
                    v-model="entity.amount" />
            </div>
            <feedback :valid="false" :message="errors.fields['amount']" />
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import axios from 'axios'
import AlertMessage from '../AlertMessage'
import Feedback from '../Feedback'
import { emptyErrors, parseResponseError } from '../../utils/errors'
import { CurrencyInput } from 'vue-currency-input'


const initialValue = {
    id: null,
    amount: null,
    note: '',
}

export default Vue.extend({
    components: {
        AlertMessage,
        Feedback,
        CurrencyInput
    },
    props: {
        type: {
            type: String,
            required: true,
        },
        baseUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            entity: { ...initialValue },
            errors: emptyErrors()
        }
    },
    methods: {
        reset() {
            this.entity = { ...initialValue }
            this.errors = emptyErrors()
        },
        async submit() {
            const data = { ...this.entity }
            data.amount = String(data.amount).valueOf()
            let url = `${this.baseUrl}/${this.type}`
            if (data.id) {
                url += `/${data.id}`
            }
            this.errors = emptyErrors()
            try {
                const response = await axios.post(url, data)
                this.$emit('save', response.data)
            } catch (e) {
                this.errors = parseResponseError(e)
            }
        }
    }
})
</script>
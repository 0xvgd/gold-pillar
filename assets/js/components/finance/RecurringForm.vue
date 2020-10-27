<template>
    <div>
        <alert-message color="danger" :message="errors.message" />

        <div class="form-group">
            <label class="required">Description</label>
            <input
                type="text"
                required="required"
                :class="'form-control ' + (!!errors.fields['description'] && 'is-invalid')"
                v-model="entity.description" />
            <feedback :valid="false" :message="errors.fields['description']" />
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
        <div class="form-group">
            <label class="required">Time to process</label>
            <div id="recurring_time" class="form-inline">
                <select
                    :class="'form-control ' + (!!errors.fields['time'] && 'is-invalid')"
                    v-model="timeHourPart"
                    required="required">
                    <option :key="i" :value="zeroFill(i - 1)" v-for="i in 24">
                        {{zeroFill(i - 1)}}
                    </option>
                </select>
                :
                <select
                    :class="'form-control ' + (!!errors.fields['time'] && 'is-invalid')"
                    v-model="timeMinutePart"
                    required="required">
                    <option :key="i" :value="zeroFill(i - 1)" v-for="i in 60">
                        {{zeroFill(i - 1)}}
                    </option>
                </select>
            </div>
            <feedback :valid="false" :message="errors.fields['time']" />
        </div>
        <div class="form-group">
            <label>Recurring</label>
            <div class="input-group">
                <select class="form-control" v-model="entity.interval" required="required">
                    <option value="day">Daily</option>
                    <option value="week">Weekly</option>
                    <option value="month">Monthly</option>
                    <option value="year">Yearly</option>
                </select>
                <select class="form-control" v-model="dayOfWeek" v-if="entity.interval === 'week'">
                    <option :value="1">Monday</option>
                    <option :value="2">Tuesday</option>
                    <option :value="3">Wednesday</option>
                    <option :value="4">Thursday</option>
                    <option :value="5">Friday</option>
                    <option :value="6">Saturday</option>
                    <option :value="7">Sunday</option>
                </select>
                <select class="form-control" v-model="dayOfMonth" v-if="entity.interval === 'month'">
                    <option :value="1">1st</option>
                    <option :value="2">2nd</option>
                    <option :value="3">3rd</option>
                    <option :value="4">4th</option>
                    <option :value="5">5th</option>
                    <option :value="6">6th</option>
                    <option :value="7">7th</option>
                    <option :value="8">8th</option>
                    <option :value="9">9th</option>
                    <option :value="10">10th</option>
                    <option :value="11">11th</option>
                    <option :value="12">12th</option>
                    <option :value="13">13th</option>
                    <option :value="14">14th</option>
                    <option :value="15">15th</option>
                    <option :value="16">16th</option>
                    <option :value="17">17th</option>
                    <option :value="18">18th</option>
                    <option :value="19">19th</option>
                    <option :value="20">20th</option>
                    <option :value="21">21th</option>
                    <option :value="22">22th</option>
                    <option :value="23">23th</option>
                    <option :value="24">24th</option>
                    <option :value="25">25th</option>
                    <option :value="26">26th</option>
                    <option :value="27">27th</option>
                    <option :value="28">28th</option>
                    <option :value="29">29th</option>
                    <option :value="30">30th</option>
                    <option :value="31">31th</option>
                </select>
                <select class="form-control" v-model="monthOfYear" v-if="entity.interval === 'year'">
                    <option :value="1">January</option>
                    <option :value="2">February</option>
                    <option :value="3">March</option>
                    <option :value="4">April</option>
                    <option :value="5">May</option>
                    <option :value="6">June</option>
                    <option :value="7">July</option>
                    <option :value="8">August</option>
                    <option :value="9">September</option>
                    <option :value="10">October</option>
                    <option :value="11">November</option>
                    <option :value="12">December</option>
                </select>
            </div>
            <feedback :valid="false" :message="errors.fields['interval']" />
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import axios from 'axios'
import AlertMessage from '../AlertMessage'
import Feedback from '../Feedback'
import { CurrencyInput } from 'vue-currency-input'
import { emptyErrors, parseResponseError } from '../../utils/errors'

const initialValue = {
    id: null,
    amount: null,
    description: '',
    interval: 'day',
    dayOrMonth: 1,
    time: '',
}

export default Vue.extend({
    components: {
        AlertMessage,
        Feedback,
        CurrencyInput,
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
            errors: emptyErrors(),
            dayOfWeek: 1,
            dayOfMonth: 1,
            monthOfYear: 1,
            timeHourPart: null,
            timeMinutePart: null,
        }
    },
    methods: {
        zeroFill(value) {
            value = `0${value}`
            return value.substring(value.length - 2)
        },
        reset() {
            this.entity = { ...initialValue }
            this.errors = emptyErrors()
            this.dayOfWeek = 1
            this.dayOfMonth = 1
            this.monthOfYear = 1
            this.timeHourPart = null
            this.timeMinutePart = null
        },
        async edit(id) {
            let url = `${this.baseUrl}/recurring-${this.type}/${id}`
            const response = await axios.get(url)
            this.entity = response.data
            this.entity.interval = response.data.interval.value
            let time = this.entity.time.split('T')[1]
            time = time.substring(0, 5).split(':')
            this.timeHourPart = time[0]
            this.timeMinutePart = time[1]
            this.dayOfWeek = this.entity.dayOrMonth
            this.dayOfMonth = this.entity.dayOrMonth
            this.monthOfYear = this.entity.dayOrMonth
        },
        async submit() {
            const data = { ...this.entity }
            data.amount = String(data.amount).valueOf()
            data.time = `${this.timeHourPart}:${this.timeMinutePart}`
            switch (data.interval) {
                case 'week':
                    data.dayOrMonth = this.dayOfWeek
                    break;
                case 'month':
                    data.dayOrMonth = this.dayOfMonth
                    break;
                case 'year':
                    data.dayOrMonth = this.monthOfYear
                    break;
            }
            let url = `${this.baseUrl}/recurring-${this.type}`
            if (data.id) {
                url += `/${data.id}`
            }
            try {
                const response = await axios.post(url, data)
                this.entity = response.data
                this.$emit('save', response.data)
            } catch (e) {
                this.errors = parseResponseError(e)
            }
            
        }
    }
})
</script>
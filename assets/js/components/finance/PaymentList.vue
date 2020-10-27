<template>
    <div class="table-responsive">
        <table class="table border">
            <thead>
                <tr>
                    <th>{{ 'Date' | trans }}</th>
                    <th>{{ 'Note' | trans }}</th>
                    <th>{{ 'Amount' | trans }}</th>
                    <th>{{ 'Recurring' | trans }}</th>
                    <th>{{ 'Transaction ID' | trans }}</th>
                    <th>{{ 'Transaction Status' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr :key="payment.id" v-for="payment in entities">
                    <td>{{ payment.createdAt|dateTime() }}</td>
                    <td>{{ payment.note }}</td>
                    <td>{{ payment.amount|currency() }}</td>
                    <td>{{ payment.recurring ? 'Yes' : 'No' }}</td>
                    <td>
                        <transaction-id :transaction="payment.transaction" />
                    </td>
                    <td>
                        <transaction-status :transaction="payment.transaction" />
                    </td>
                </tr>
                <tr v-if="entities.length === 0">
                    <td colspan="6" class="text-center text-muted">
                        {{ 'No records found' | trans }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex">
            <pagination
                :hasPrev="paginator.hasPrev()"
                :hasNext="paginator.hasNext()"
                @next="next"
                @prev="prev"
                />
            <div class="ml-auto">
                <button type="button" class="btn btn-secondary btn-sm" @click="add">
                    <i class="fa fa-plus"></i>
                    {{ 'New record'| trans }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import { Paginator } from '../../utils/paginator'
import Pagination from '../Pagination'
import TransactionId from './TransactionId'
import TransactionStatus from './TransactionStatus'

export default Vue.extend({
    components: {
        Pagination,
        TransactionId,
        TransactionStatus,
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
        pageSize: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            entities: [],
            paginator: new Paginator(`${this.baseUrl}/${this.type}`, this.pageSize)
        }
    },
    methods: {
        async fetch() {
            await this.paginator.fetch()
            this.entities = this.paginator.getItems()
        },
        async next() {
            await this.paginator.next()
            this.entities = this.paginator.getItems()
        },
        async prev() {
            await this.paginator.prev()
            this.entities = this.paginator.getItems()
        },
        add() {
            this.$emit('new')
        }
    },
    beforeMount() {
        this.fetch()
    }
})
</script>
<template>
    <div class="table-responsive">
        <table class="table border">
            <thead>
                <tr>
                    <th>{{ 'Date' | trans }}</th>
                    <th>{{ 'Note' | trans }}</th>
                    <th>{{ 'Transaction ID' | trans }}</th>
                    <th>{{ 'Amount' | trans }}</th>
                    <th>{{ 'Status' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr :key="transaction.id" v-for="transaction in entities">
                    <td>{{ transaction.createdAt|dateTime() }}</td>
                    <td>{{ transaction.note }}</td>
                    <td>
                        <transaction-id :transaction="transaction" />
                    </td>
                    <td :class="transaction.credit ? 'text-success' : 'text-danger'">
                        <i class="fa fa-arrow-up" v-if="transaction.credit"></i>
                        <i class="fa fa-arrow-down" v-else></i>
                        {{ transaction.amount|currency() }}
                    </td>
                    <td>
                        <transaction-status :transaction="transaction" />
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
            paginator: new Paginator(`${this.baseUrl}/transactions`, this.pageSize)
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
    },
    beforeMount() {
        this.fetch()
    }
})
</script>
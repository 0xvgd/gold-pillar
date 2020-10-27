<template>
    <div class="table-responsive">
        <table class="table border">
            <thead>
                <tr>
                    <th>{{ 'Date' | trans }}</th>
                    <th>{{ 'Transaction ID' | trans }}</th>
                    <th>{{ 'Amount' | trans }}</th>
                    <th>{{ 'Fee' | trans }}</th>
                    <th>{{ 'Total' | trans }}</th>
                    <th>{{ 'Transaction Status' | trans }}</th>
                    <th>{{ 'Invest Status' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr :key="investment.id" v-for="investment in entities">
                    <td>{{ investment.createdAt|dateTime() }}</td>
                    <td>
                        <transaction-id :transaction="investment.transaction" />
                    </td>
                    <td>{{ investment.amount|currency() }}</td>
                    <td>{{ investment.fee|currency() }}</td>
                    <td>{{ investment.transaction.amount|currency() }}</td>
                    <td>
                        <transaction-status :transaction="investment.transaction" />
                    </td>
                    <td>
                        <investment-status :investment="investment" />
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
import TransactionId from './TransactionId.vue'
import TransactionStatus from './TransactionStatus.vue'
import InvestmentStatus from './InvestmentStatus.vue'

export default Vue.extend({
    components: {
        Pagination,
        TransactionId,
        TransactionStatus,
        InvestmentStatus,
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
            paginator: new Paginator(`${this.baseUrl}/investments`, this.pageSize)
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
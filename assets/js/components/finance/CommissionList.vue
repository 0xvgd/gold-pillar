<template>
    <div class="table-responsive">
        <table class="table border">
            <thead>
                <tr>
                    <th>{{ 'Date' | trans }}</th>
                    <th>{{ 'Transaction ID' | trans }}</th>
                    <th>{{ 'Note' | trans }}</th>
                    <th>{{ 'Ref amount' | trans }}</th>
                    <th>{{ 'Fee' | trans }}</th>
                    <th>{{ 'commission' | trans }}</th>
                    <th>{{ 'Transaction Status' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr :key="commission.id" v-for="commission in entities">
                    <td>{{ commission.createdAt|dateTime() }}</td>
                    <td>
                        <transaction-id :transaction="commission.transaction" />
                    </td>
                    <td>{{ commission.note }}</td>
                    <td>{{ commission.commission.refAmount|currency() }}</td>
                    <td>{{ ((commission.commission.fee || 0) * 100).toFixed(2) }}%</td>
                    <td>{{ commission.transaction.amount|currency() }}</td>
                    <td>
                        <transaction-status :transaction="commission.transaction" />
                    </td>
                </tr>
                <tr v-if="entities.length === 0">
                    <td colspan="7" class="text-center text-muted">
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
            paginator: new Paginator(`${this.baseUrl}/commission/payments`, this.pageSize)
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
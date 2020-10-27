<template>
    <div class="table-responsive">
        <table class="table border">
            <thead>
                <tr>
                    <th>{{ 'Date' | trans }}</th>
                    <th>{{ 'Description' | trans }}</th>
                    <th>{{ 'Amount' | trans }}</th>
                    <th>{{ 'Recurring' | trans }}</th>
                    <th>{{ 'Time to process' | trans }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr :key="recurring.id" v-for="recurring in entities">
                    <td>{{ recurring.createdAt|dateTime }}</td>
                    <td>{{ recurring.description }}</td>
                    <td>{{ recurring.amount|currency }}</td>
                    <td>{{ recurring.formattedLabel }}</td>
                    <td>{{ recurring.time|time }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-link text-dark btn-sm"
                            @click="edit(recurring)">
                            <i class="fa fa-pen"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-link text-danger btn-sm"
                            @click="remove(recurring)">
                            <i class="fa fa-trash"></i>
                        </button>
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

export default Vue.extend({
    components: {
        Pagination,
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
            paginator: new Paginator(`${this.baseUrl}/recurring-${this.type}`, this.pageSize)
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
        },
        edit(item) {
            this.$emit('edit', item)
        },
        async remove(item) {
            if (confirm('Do you really want remove it?')) {
                await this.paginator.removeItem(item)
                this.entities = this.paginator.getItems()
                this.fetch()
            }
        }
    },
    beforeMount() {
        this.fetch()
    }
})
</script>
import axios from 'axios'

export class Paginator {
    constructor(baseUrl, pageSize) {
        this.page = 1
        this.pageSize = pageSize
        this.items = []
        this.baseUrl = baseUrl
        this.loading = false
    }

    isLoading() {
        return this.loading
    }

    hasPrev() {
        return this.page > 1
    }

    hasNext() {
        return this.items.length >= this.pageSize
    }

    getItems() {
        return this.items
    }

    async removeItem(item) {
        if (this.loading) {
            return
        }
        this.loading = true
        try {
            await axios.delete(`${this.baseUrl}/${item.id}`)
            this.items.splice(this.items.indexOf(item), 1)
        } catch (e) {
            console.error(e)
        } finally {
            this.loading = false
        }
    }

    async prev() {
        if (!this.loading && this.hasPrev()) {
            this.page--
            await this.fetch()
        }
    }
    async next() {
        if (!this.loading && this.hasNext()) {
            this.page++
            await this.fetch()
        }
    }
    async fetch() {
        if (this.loading) {
            return
        }
        this.loading = true
        try {
            let url = `${this.baseUrl}?page=${this.page}`
            const response = await axios.get(url)
            this.items = response.data
        } catch (e) {
            console.error(e)
        } finally {
            this.loading = false
        }
    }
}
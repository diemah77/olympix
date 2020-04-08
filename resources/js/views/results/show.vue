<template>
<div>
    {{ phase.name }}
</div>
</template>

<script>
import front from '@/views/layouts/front'
import results from '@/views/layouts/results'

export default {
	props: {
        phase: Object,
    },

    layout: (h, page) => {
        return h(front, {} , [
            h(results, {}, [page]),
        ])
    },

    data()
    {
        return {
            perPage: 10,
            currentPage: 1
        }
    },

    computed: {
        // paginatedTournaments()
        // {
        //     return this.tournaments.slice(Math.max(this.currentPage - 1, 0) * this.perPage, Math.max(this.currentPage, 1) * this.perPage)
		// },

		// pageCount()
		// {
		// 	return Math.ceil(this.tournaments.length / this.perPage)
		// }
    },

    methods: {
        goToPage(page)
		{
			this.currentPage = page
		}
    }
}
</script>

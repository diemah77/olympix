<template>
<div>
    <div v-for="(chunk, index) in chunkedTables" :key="index" class="flex">
        <olympix-table
            v-for="table in chunk"
            :key="table.id"
            :table="table"
            :draw="false">
        </olympix-table>
    </div>
</div>
</template>

<script>
import chunk from '@/mixins/chunk'
import admin from '@/views/layouts/admin'
import schedule from '@/views/layouts/schedule'
import OlympixTable from '@/components/olympix-table'

export default {
   layout: (h, page) => {
        return h(admin, {} , [
            h(schedule, {}, [page]),
        ])
    },

    components: {
        OlympixTable
    },

    props: {
        tournament: Object,
        matches: Array,
        tables: Array,
    },

    methods: {
         chunk: chunk
    },

    computed: {
        chunkedTables()
        {
            const tables = this.chunk(this.tables, Math.ceil(this.tables.length / this.tournament.tables_rows))

            if (this.tournament.tables_transponed)
            {
                return tables.reduce((r, a, i, { length }) =>
                {
                    a.forEach((v, j) =>
                    {
                        r[j] = r[j] || []
                        r[j][i] = v
                    })

                    return r
                }, [])
            }

            return tables
        }
    }
}
</script>

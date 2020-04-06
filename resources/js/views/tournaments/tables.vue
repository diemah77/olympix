<template>
<div>
    <box class="flex items-center mb-6">
        <div class="flex items-center">
            <label class="mr-4 text-sm font-bold">Anzahl Reihen</label>
            <el-input-number
                @change="update()"
                v-model="form.tables_rows"
                :min="1"
                :max="form.tables_count">
            </el-input-number>
        </div>

        <div class="flex items-center ml-auto">
            <el-button type="primary" @click="transpose()">Reihen spiegeln</el-button>
            <el-button type="primary" @click="rotate()">Tische um 90° drehen</el-button>
        </div>
    </box>

    <div v-for="(chunk, index) in chunkedTables" :key="index" class="flex">
        <olympix-table
            v-for="table in chunk"
            :key="table.id"
            :table="table"
            :draw="true">
        </olympix-table>
    </div>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import tournament from '@/views/layouts/tournament'
import OlympixTable from '@/components/olympix-table'
import chunk from '@/mixins/chunk'

export default {
    components: {
        OlympixTable
    },

    mixins: [
        validation
    ],

    props : {
        tournament: {
            type: Object,
            required: true
        },

        initialTables: {
            type: Array,
            required: true
        }
    },

    layout: (h, page) => {
        return h(admin, {} , [
            h(tournament, {
                props: {
                    tournament: page.data.props.tournament,
                    width: 'full'
                }
            }, [page]),
        ])
    },

	data()
	{
		return {
			form: {
                ...this.tournament
            },
            tables: [...this.initialTables]
		}
    },

    computed: {
        chunkedTables()
        {
            const tables = this.chunk(this.tables, Math.ceil(this.tables.length / this.form.tables_rows))

            if (this.form.tables_transponed)
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
    },

    methods: {
        chunk: chunk,

        save()
        {
            const request = this.mode == 'edit' ? axios.put(route('tournaments.update', this.form.id).url(), this.form) : axios.post(route('tournaments.store').url(), this.form)

            request.then(response =>
            {
                this.$inertia.visit(route('tournaments.index'))
            })
            .catch(error => this.errors = error.response.data.errors)
        },

        rotate()
        {
            axios.post(route('tables.rotate', [this.tournament.id]).url())

            this.tables = this.tables.map(t =>
            {
                t.horizontal = !t.horizontal

                return t
            })
        },

        update()
        {
            this.$nextTick(() => axios.post(route('tables', [this.tournament.id]).url(), this.form))
        },

        transpose()
        {
            this.form.tables_transponed = !this.form.tables_transponed
            this.update()
        }
    }
}
</script>

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

        <div class="flex items-center ml-6">
            <el-checkbox-group v-model="form.tables_transformations" @change="handleChange">
                <el-checkbox-button label="transpose" >Um 90° drehen</el-checkbox-button>
                <el-checkbox-button label="reflect_horizontally">Horizontal spiegeln</el-checkbox-button>
                <el-checkbox-button label="reflect_vertically">Vertikal spiegeln</el-checkbox-button>
            </el-checkbox-group>
        </div>

        <div class="ml-auto">
            <el-button type="primary" plain @click="rotate()">Tische um 90° drehen</el-button>
        </div>
    </box>

    <div v-for="(chunk, index) in modifiedTables" :key="index" class="flex">
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
import sort from 'fast-sort'

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
            tables: [...this.initialTables],
            modifiedTables: []
		}
    },

    created()
    {
        this.applyTransforms()
    },

    methods: {
        chunk: chunk,
        sort: sort,

        handleChange(values)
        {
            this.applyTransforms()
            this.update()
        },

        applyTransforms()
        {
            this.modifiedTables = this.chunk(this.tables, Math.ceil(this.tables.length / this.form.tables_rows))

            if (this.form.tables_transformations.some(t => t == 'transpose'))
            {
                this.transpose()
            }

            if (this.form.tables_transformations.some(t => t == 'reflect_horizontally'))
            {
                this.reflectHorizontally()
            }

            if (this.form.tables_transformations.some(t => t == 'reflect_vertically'))
            {
                this.reflectVertically()
            }
        },

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
            this.modifiedTables = this.modifiedTables.reduce((r, a, i, { length }) =>
                {
                    a.forEach((v, j) =>
                    {
                        r[j] = r[j] || []
                        r[j][i] = v
                    })

                    return r
                }, [])
        },

        reflectHorizontally()
        {
            this.modifiedTables = this.modifiedTables.map(row => row.reverse())
        },

        reflectVertically()
        {
            let tables = this.modifiedTables.reduce((r, a, i, { length }) =>
                {
                    a.forEach((v, j) =>
                    {
                        r[j] = r[j] || []
                        r[j][i] = v
                    })

                    return r
                }, [])

            tables = tables.map(row => row.reverse())

            tables = tables.reduce((r, a, i, { length }) =>
                {
                    a.forEach((v, j) =>
                    {
                        r[j] = r[j] || []
                        r[j][i] = v
                    })

                    return r
                }, [])

            this.modifiedTables = tables
        }
    }
}
</script>

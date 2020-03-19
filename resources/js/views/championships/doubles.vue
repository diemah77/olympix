<template>
<div class="mt-8">
    <div class="flex items-center mb-6">
        <label class="label mr-4">Losmethode: </label>
        <el-radio-group class="pr-2" v-model="championship.doubles_draw_type">
            <el-radio-button
                v-for="type in championship.doublesDrawList"
                :label="type.id"
                :key="type.id">

                {{ type.label }}
            </el-radio-button>
        </el-radio-group>

        <el-button class="ml-auto" type="primary" @click="drawDoubles">
            <icon class="mr-1" icon="random" fixed-width></icon>
            Doppel auslosen
        </el-button>

        <el-button :disabled="!championship.participants.length" type="danger" @click="resetDoubles">
            <icon class="mr-1" icon="undo" fixed-width></icon>
            Reset
        </el-button>
    </div>

    <div class="flex">
        <div class="w-1/2 mr-8">
            <el-table
                :data="championship.registrations"
                empty-text="Keine Spieler gemeldet">

                <el-table-column
                    label="Name"
                    property="fullname">
                </el-table-column>

                <el-table-column
                    label="TTR-Punkte"
                    property="ttr"
                    width="120">
                </el-table-column>
            </el-table>
        </div>

        <div class="w-1/2 ml-8">
            <el-table
                :data="orderedParticipants"
                empty-text="Keine Doppel gelost">

                <el-table-column
                    label="Geloste Doppel"
                    property="fullname">
                </el-table-column>
            </el-table>
        </div>
    </div>
</div>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import championship from '@/views/layouts/championship'

export default {
     props : {
        championship: Object
    },

    layout: (h, page) => {
        return h(admin, {
            props: {title: 'Doppelpaarungen'}
        } , [
            h(championship, {props: {championship: page.data.props.championship, width: 'narrow'}}, [page]),
        ])
    },

	data()
	{
		return {
            form: {...this.championship}
		}
    },

    computed: {
        orderedParticipants()
		{
			return sort(this.championship.participants).by([{desc: 'ttr'}])
        }
    },

    methods: {
        sort: sort,

        drawDoubles()
		{
			axios.post(route('doubles.store', [this.$page.t.id, this.championship.id]), {mode: this.championship.doubles_draw_type}).then(response =>
			{
				this.$inertia.reload()
			})
        },

        resetDoubles()
		{
            axios.delete(route('doubles.destroy', [this.$page.t.id, this.championship.id])).then(response =>
			{
				this.$inertia.reload()
			})
		}
    }
}
</script>

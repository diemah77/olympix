<template>
<div class="flex mt-8">
    <div class="w-3/5 pr-8">
        <el-table
            :data="orderedParticipants"
            :empty-text="'Kein ' + participantEntity +  ' gemeldet'">

            <!-- <el-table-column
                type="index"
                width="50">
            </el-table-column> -->

            <el-table-column
                label="Name"
                sortable
                property="fullname">
            </el-table-column>

            <!-- <el-table-column
                label="TTR-Punkte"
                sortable
                property="ttr"
                width="100">
            </el-table-column> -->

            <el-table-column
                label="Gesetzt"
                label-class-name="text-center"
                class-name="text-center"
                width="80">

                <el-checkbox slot-scope="scope" v-model="scope.row.isSeeded" @change="toggleSeeding($event, scope.row)"></el-checkbox>
            </el-table-column>
        </el-table>
    </div>

    <div class="w-2/5 ml-8">
        <h1 class="text-sm pt-4 mt-1 mb-2 font-bold">Setzliste</h1>

        <draggable v-if="seedings.length" v-model="orderedSeedings">
            <transition-group>
                <div class="flex mb-3 px-2 py-4 shadow text-sm" v-for="s in orderedSeedings" :key="s.id">
                    <icon class="mr-4 text-gray-600 cursor-move" icon="bars" fixed-width></icon>
                    <span class="font-bold mr-2 cursor-move">{{ s.seed }}.</span>{{ s.fullname }}
                    <icon class="ml-auto text-gray-600 hover:text-gray-800 cursor-pointer" icon="times" fixed-width @click="removeSeeding(s)"></icon>
                </div>
            </transition-group>
        </draggable>

        <p v-else class="py-3 text-gray-700 text-sm">{{ form.isSingles ? 'Kein Spieler gesetzt' : 'Kein Doppel gesetzt'}}</p>
    </div>
</div>
</template>

<script>
import sort from 'fast-sort'
import draggable from 'vuedraggable'
import admin from '@/views/layouts/admin'
import championship from '@/views/layouts/championship'

export default {
    components: {
          draggable,
    },

    layout: (h, page) => {
        return h(admin, {
            props: {title: 'Setzliste'}
        } , [
            h(championship, {props: {championship: page.data.props.championship, width: 'narrow'}}, [page]),
        ])
    },

    props : {
        championship: Object,
        initialParticipants: Array,
    },

	data()
	{
		return {
            form: {...this.championship},
            participants: this.initialParticipants,
		}
    },

    computed: {
        participantEntity()
		{
			return this.form.isSingles ? 'Spieler' : 'Doppel'
		},

        canSeed()
		{
			return this.participants.length > 0
        },

        seedings()
		{
			return this.participants.filter(p => p.isSeeded)
        },

        orderedSeedings:
		{
			get()
			{
				return sort(this.seedings).asc(s => s.seed)
			},

			set(newArr)
			{
				this.resetSeedingOrder(newArr)
				this.updateAllSeedings()
			}
		},

        orderedParticipants()
		{
			return sort(this.participants).by([{desc: 'ttr'}])
        }
    },

    methods: {
        sort: sort,

        save()
        {
            if (this.mode == 'edit')
            {
                axios.put(route('championships.update', [this.$page.t.id, this.form.id]), this.form).then(() =>
                {
                    this.$message.success('Spielklasse aktualisiert.')
                })
                .catch(error => this.errors = error.response.data.errors)
            }
            else
            {
                axios.post(route('championships.store', [this.$page.t.id]), this.form).then(response =>
                {
                    this.$inertia.visit(route('championships.edit', [this.$page.t.id, response.data.id]))
                })
                .catch(error => this.errors = error.response.data.errors)
            }
        },

        toggleSeeding(isSeeded, participant)
        {
        	isSeeded ? participant.seed = this.seedings.length + 1 : participant.seed = ''

        	this.resetSeedingOrder(this.orderedSeedings)
        	this.updateAllSeedings()
        },

        removeSeeding(participant)
        {
            participant.seed = ''
            participant.isSeeded = false
            this.resetSeedingOrder(this.orderedSeedings)
            this.updateAllSeedings()
        },

        resetSeedingOrder(seedings)
        {
        	seedings.map((p, index) =>
			{
				let participant = this.participants.find(pp => pp.id == p.id)

				participant.seed = (index + 1)
			})
        },

        updateAllSeedings()
        {
            axios.put(route('participants.update', [this.$page.t.id, this.form.id]), {participants: this.participants}).then(() => this.$message.success('Setzung aktualisiert!'))
        }
    }
}
</script>

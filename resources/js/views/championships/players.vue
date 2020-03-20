<template>
<div>
    <div class="flex justify-between items-center mb-4 pt-8">
        <div class="w-1/2">
            <el-alert
                v-if="registrations.length == 0"
                :title="registrations.length + ' von ' + players.length + ' Spieler gemeldet'"
                type="info"
                description="Noch keinen Spieler gemeldet"
                :closable="false"
                show-icon>
            </el-alert>

            <el-alert
                v-else-if="registrations && registrations.length != players.length"
                :title="registrations.length + ' von ' + players.length + ' Spieler gemeldet'"
                type="warning"
                description="Nicht alle Spieler für diese Klasse gemeldet!"
                :closable="false"
                show-icon>
            </el-alert>

            <el-alert
                v-else
                :title="registrations.length + ' von ' + players.length + ' Spieler gemeldet'"
                type="success"
                description="Alle Spieler für diese Klasse gemeldet!"
                :closable="false"
                show-icon>
            </el-alert>
        </div>

        <el-button :disabled="registrations.length == players.length" type="primary" @click="registerAll">Alle Spieler melden</el-button>
    </div>

    <el-table
        v-if="players.length"
        :data="players"
        empty-text="Keine Spieler erfasst">

        <el-table-column
            type="index"
            width="50">
        </el-table-column>

        <el-table-column
            label="Name"
            property="fullname">
        </el-table-column>

        <el-table-column
            label="TTR-Punkte"
            property="ttr">
        </el-table-column>

        <el-table-column
            label="Gemeldet"
            label-class-name="text-center"
            class-name="text-center">

            <el-checkbox
                :disabled="championship.hasStartedPhase"
                slot-scope="scope"
                v-model="scope.row.isRegistered"
                @change="toggleRegistration($event, scope.row)">
            </el-checkbox>
        </el-table-column>
    </el-table>

    <div v-else class="flex justify-center mt-8">
        <el-button type="primary" @click="$inertia.visit(route('players.index', $page.t.id))">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            Jetzt Spieler erfassen
        </el-button>
    </div>
</div>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import championship from '@/views/layouts/championship'

export default {
     props : {
        championship: Object,
        initialRegistrations: Array,
        initialPlayers: Array,
    },

    layout: (h, page) => {
        return h(admin, {
            props: {title: 'Gemeldete Spieler'}
        } , [
            h(championship, {props: {championship: page.data.props.championship, width: 'narrow'}}, [page]),
        ])
    },

	data()
	{
		return {
            form: {...this.championship},
            registrations: this.initialRegistrations,
            players: this.initialPlayers
		}
    },

    computed: {
        isSaved()
        {
            return this.mode == 'edit'
        },

        participantEntity()
		{
			return this.form.isSingles ? 'Spieler' : 'Doppel'
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
                axios.post(route('championships.store', [this.$page.t.id]).url(), this.form).then(response =>
                {
                    this.$inertia.visit(route('championships.edit', [this.$page.t.id, response.data.id]).url())
                })
                .catch(error => this.errors = error.response.data.errors)
            }
        },

        registerAll()
        {
            this.players.map(p => p.isRegistered = true)

            axios.post(route('registrations.store-all', [this.$page.t.id, this.form.id]).url(), {players: this.players}).then(response =>
        	{
        		this.registrations = response.data.registrations
        		this.registrations_count = response.data.registrations.length
        		this.$message.success('Alle Spieler gemeldet!')
        	})
        },

        toggleRegistration(toBeRegistered, player)
        {
        	let service, msg

        	if (toBeRegistered)
        	{
        		service = axios.post(route('registrations.store', [this.$page.t.id, this.form.id, player.id]).url())
        		msg = 'Spieler gemeldet!'
        	}
        	else
        	{
        		service = axios.delete(route('registrations.destroy', [this.$page.t.id, this.form.id, player.id]).url())
        		msg = 'Spieler abgemeldet!'
        	}

        	service.then(response => {
        			if (toBeRegistered)
	        		{
                        // player.isRegistered = true
	        			this.registrations.push(response.data.registration)
	        		}
	        		else
	        		{
	        			this.registrations = this.registrations.filter(r => r.player_id != player.id)
	        		}

	        		this.$message.success(msg)
        		// })
        	})
        },
    }
}
</script>

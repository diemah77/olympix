<template>
<div>
    <box>
        <div class="flex">
            <div class="mt-4 flex-1">
                <h1 class="w-1/3 font-normal text-xl mb-6">Grunddaten</h1>

                <div class="flex items-center mb-8">
                    <label class="label w-1/5 text-right mr-8">Vorname <span class="text-red-600">*</span></label>
                    <el-input @input="clear('firstname')" class="w-1/3" v-model="form.firstname"></el-input>
                    <span v-if="has('firstname')" class="ml-3 text-red-600 text-xs">{{ get('firstname') }}</span>
                </div>

                <div class="flex items-center mb-8">
                    <label class="label w-1/5 text-right mr-8">Nachname <span class="text-red-600">*</span></label>
                    <el-input @input="clear('lastname')" class="w-1/3" v-model="form.lastname"></el-input>
                    <span v-if="has('lastname')" class="ml-3 text-red-600 text-xs">{{ get('lastname') }}</span>
                </div>

                <div class="flex items-center mb-8">
                    <label class="label w-1/5 text-right mr-8">TTR-Punkte <span class="text-red-600">*</span></label>
                    <el-input @input="clear('ttr')" class="w-1/5" v-model="form.ttr"></el-input>
                    <span v-if="has('ttr')" class="ml-3 text-red-600 text-xs">{{ get('ttr') }}</span>
                </div>

                <div class="flex mb-8">
                    <div class="w-4/5 pl-8 ml-auto">
                        <el-button @click="$inertia.visit(route('players.index', [$page.t.id]))">Abbrechen</el-button>
                        <el-button type="primary" @click="save">Speichern</el-button>
                    </div>
                </div>

                <transition name="fade">
                    <div class="mt-8" v-if="isSaved">
                        <h1 class="w-1/3 font-normal text-xl mb-6">Gemeldete Klassen</h1>

                        <div v-if="championships.length" v-for="championship in championships" :key="championship.id" class="flex items-center mb-8">
                            <label class="label w-1/5 text-right mr-8">{{ championship.name }}</label>
                            <el-checkbox :checked="isRegistered(championship)" @change="toggle($event, championship)"></el-checkbox>
                        </div>

                        <p v-else>Keine Spielklassen erstellt</p>
                    </div>
                </transition>
            </div>
        </div>
    </box>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'

export default {
    mixins: [
        validation
    ],

    props: {
        player: Object,
        championships: Array,
        mode: {
            default: 'create'
        }
    },

    layout: (h, page) => h(admin, {
        props: {
            title: page.data.props.mode == 'create' ? 'Spieler erfassen' : page.data.props.player.fullname
        }
    } ,  [page]),

	data()
	{
		return {
			form: {...this.player}
		}
	},

    computed: {
        isSaved()
        {
            return this.mode == 'edit'
        }
    },

	methods: {
        // TODO: ausführen nur, wenn die Klasse vorbereitet
        toggle(event, championship)
        {
            if (event)
            {
                axios.post(route('registrations.store', [this.$page.t.id, championship.id, this.form.id])).then(() =>
                {
                    this.form.championships.push(championship)
                    this.$message.success('Spieler gemeldet!')
                })
            }
            else
            {
                axios.delete(route('registrations.destroy', [this.$page.t.id, championship.id, this.form.id])).then(() =>
                {
                    this.form.championships.filter(c => c.id != championship.id)
                    this.$message.success('Spieler abgemeldet!')
                })
            }
        },

        isRegistered(championship)
        {
            return this.player.championships.some(c => c.id == championship.id)
        },

        save()
        {
            if (this.mode == 'edit')
            {
                axios.put(route('players.update', [this.$page.t.id, this.form.id]), this.form).then(() =>
                {
                    this.$message.success('Spieler aktualisiert.')
                })
                .catch(error => this.errors = error.response.data.errors)
            }
            else
            {
                axios.post(route('players.store', [this.$page.t.id]), this.form).then(response =>
                {
                    this.$inertia.visit(route('players.edit', [this.$page.t.id, response.data.id]))
                })
                .catch(error => this.errors = error.response.data.errors)
            }
        }
	}
}
</script>

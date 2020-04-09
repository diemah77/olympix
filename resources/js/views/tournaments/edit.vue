<template>
<box>
    <div class="flex">
        <div class="mt-4 flex-1">
            <div class="flex items-center mb-8">
                <label class="label w-1/5 text-right mr-8">Turniername <span class="text-red-600">*</span></label>
                <el-input @input="clear('name')" class="w-2/5" v-model="form.name" clearable></el-input>
                <span v-if="has('name')" class="ml-3 text-red-600 text-xs">{{ get('name') }}</span>
            </div>

            <div class="flex items-center mb-8">
                <label class="label w-1/5 text-right mr-8">Anzahl Tische <span class="text-red-600">*</span></label>
                <el-input-number
                    :disabled="disableTables"
                    @input="clear('tables_count')"
                    v-model="form.tables_count">
                </el-input-number>
                <span v-if="has('tables_count')" class="ml-3 text-red-600 text-xs">{{ get('tables_count') }}</span>
            </div>

            <template v-if="tournament.id">
                <div class="flex items-center mb-8">
                    <label class="label w-1/5 text-right mr-8">URL</label>
                    <a class="link text-blue-600 hover:text-blue-400 hover:underline" :href="tournament.results_route" target="_blank">{{ tournament.results_route }}</a>
                </div>

                <div class="flex mb-16">
                    <label class="label w-1/5 text-right mr-8">QR-Code</label>

                    <div class="flex flex-col">
                        <img :src="'/qrcodes/' + tournament.qr_code_src">

                        <el-button type="primary" round @click="download()">
                            <icon icon="cloud-download-alt" fixed-width class="mr-2" />
                            <span>QR-Code downloaden</span>
                        </el-button>
                    </div>
                </div>
            </template>

            <div class="flex">
                <div class="w-4/5 pl-8 ml-auto">
                    <el-button @click="$inertia.visit(route('tournaments.index'))">Abbrechen</el-button>
                    <el-button type="primary" @click="save">Speichern</el-button>
                </div>
            </div>
        </div>

        <!-- <div v-if="tournament.published" class="w-2/5 pl-6 border-l border-gray-300">
            <h1 class="text-lg font-bold mb-6">Live-Ergebnisse</h1>

            <div class="mb-8">
                <label class="label block">URL</label>
                <a class="link text-blue-600 hover:text-blue-400 hover:underline" :href="tournament.results_route" target="_blank">{{ tournament.results_route }}</a>
            </div>

            <div class="mb-8">
                <img :src="'/qrcodes/' + tournament.qr_code_src">
            </div>
        </div> -->
    </div>
</box>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import tournament from '@/views/layouts/tournament'

export default {
    mixins: [
        validation
    ],

    props : {
        tournament: {
            type: Object,
            required: true
        },

        mode: {
            type: String,
            default: 'create'
        }
    },

    layout: (h, page) => {
        return h(admin, {} , [
            h(tournament, {
                props: {
                    tournament: page.data.props.tournament
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
            disableTables: this.mode == 'edit' ? true : false
		}
	},

    methods: {
        download()
        {
            window.location.href = route('tournaments.qr_code', [this.form.id]).url()
        },

        save()
        {
            const request = this.mode == 'edit' ? axios.put(route('tournaments.update', this.form.id).url(), this.form) : axios.post(route('tournaments.store').url(), this.form)

            request.then(response =>
            {
                this.$inertia.visit(route('tournaments.index'))
            })
            .catch(error => this.errors = error.response.data.errors)
        }
    }
}
</script>

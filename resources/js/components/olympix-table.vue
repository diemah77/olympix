<template>
<div class="my-8 mx-8">
    <div class="flex flex-col items-center">
        <template v-if="!table.horizontal">

            <!-- Tisch -->
            <div class="flex flex-col flex-1" :style="{width: table.horizontal ? tableHeight + 'px' : tableWidth + 'px', height: table.horizontal ? tableWidth + 'px': tableHeight + 'px'}">

                <!-- Obere Hälfte -->
                <div class="flex-1 flex upper-half">
                    <div class="side-div bg-transparent upper-left-net"></div>

                    <div class="flex flex-1 upper-table-area shadow relative"
                        :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}">

                        <!-- Spieler A -->
                        <div v-if="table.busy && table.match" class="absolute inset-0 flex z-10">
                            <div class="m-auto mb-6 -ml-6 rounded-full font-bold text-xs py-1 px-3 bg-white shadow-lg flex items-center min-w-full">

                            <status-pulse class="flex-none" />
                            <span class="ml-2 truncate">{{ table.match.p1 }}</span></div>
                        </div>

                        <div class="w-1/2 left-upper-half relative">
                            <div class="w-8 h-8 bg-white absolute flex justify-center items-center font-bold rounded-br-lg">{{ table.name }}</div>
                        </div>
                        <div class="w-1/2 right-upper-half"></div>
                    </div>

                    <div class="side-div bg-transparent upper-right-net"></div>
                </div>

                <!-- Netz -->
                <div class="net"></div>

                <!-- Untere Hälfte -->
                <div class="flex-1 flex lower-half">
                    <div class="side-div bg-transparent lower-left-net"></div>

                    <div class="flex flex-1 lower-table-area shadow relative"
                        :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}">

                        <!-- Spieler B -->
                        <div v-if="table.busy && table.match" class="absolute inset-0 flex z-10">
                            <div class="m-auto mt-6 -mr-6 rounded-full font-bold text-xs py-1 px-3 bg-white shadow-lg text-right flex min-w-full items-center justify-end">

                                <span class="mr-2 truncate">{{ table.match.p2 }}</span>
                                <status-pulse color="orange" class="flex-none" />
                            </div>
                        </div>

                        <div class="w-1/2 left-upper-half"></div>
                        <div class="w-1/2 right-upper-half"></div>
                    </div>

                    <div class="side-div bg-transparent lower-right-net"></div>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="flex flex-col" :style="{width: table.horizontal ? tableHeight + 'px' : tableWidth + 'px', height: table.horizontal ? tableWidth + 'px': tableHeight + 'px'}">

                <!--     Obere Hälfte -->
                <div class="flex-1 flex relative">

                    <!-- Spieler A -->
                    <div v-if="table.busy && table.match" class="absolute inset-0 flex z-10">
                        <div class="m-auto mb-3 -ml-6 rounded-full font-bold text-xs py-1 px-3 bg-white shadow-lg flex items-center min-w-full">

                            <status-pulse class="flex-none" />
                            <span class="ml-2 truncate">{{ table.match.p1 }}</span>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="bg-transparent side-div-h"></div>
                        <div class="flex-1 left-upper-half-h" :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}"></div>
                    </div>

                    <div class="net-h"></div>

                    <div class="flex-1 flex flex-col relative">
                        <div class="bg-transparent side-div-h"></div>
                        <div class="flex-1 right-upper-half-h relative" :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}">

                            <!--       Tischnummer -->
                            <div class="absolute right-0 z-20 w-8 h-8 bg-white flex items-center justify-center font-bold rounded-bl-lg">{{ table.name }}</div>
                        </div>
                    </div>
                </div>

                <!--     Untere Hälfte -->
                <div class="flex-1 flex relative">

                    <!-- Spieler B -->
                    <div v-if="table.busy && table.match" class="absolute inset-0 flex z-10">
                        <div class="m-auto mt-3 -mr-6 rounded-full font-bold text-xs py-1 px-3 bg-white shadow-lg text-right flex min-w-full items-center justify-end">

                            <span class="mr-2 truncate">{{ table.match.p2 }}</span>
                            <status-pulse color="orange" class="flex-none" />
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="flex-1 left-lower-half-h" :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}"></div>
                        <div class="bg-transparent side-div-h"></div>
                    </div>

                    <div class="net-h"></div>

                    <div class="flex-1 flex flex-col relative">
                        <div class="flex-1 right-lower-half-h relative"
                            :class="{'bg-red-600': table.busy && !draw, 'bg-blue-600': !table.busy || draw}"></div>
                        <div class="bg-transparent side-div-h"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
</template>

<script>
import StatusPulse from '@/components/status-pulse'

export default {
    components: {
        StatusPulse
    },

    props: {
        draw: {
            type: Boolean,
            default: true
        },

        table: {
            type: Object,
            required: true
        }
    },

    computed: {
        tableWidth()
        {
            return this.draw ? 100 : 150
        },

        tableHeight()
        {
            const factor = 1.5

            return this.tableWidth * factor
        },

        tablePadding()
        {
            return (this.tableHeight - this.tableWidth) / 2
        },

        tableMargin()
        {
            const factor = this.table.horizontal ? 150 / this.tableWidth : 1/2

            return (this.tableHeight - this.tableWidth) * factor
        }
    },

    methods: {

    }
}
</script>

<template>
<div class="md:px-">
    <div class="bg-white z-10 mb-6 sticky top-0 relative">
        <div class="block absolute left-0 z-20">
            <el-button class="py-3 shadow-md" circle type="primary" @click="slidePrev()" :disabled="prevButtonDisabled">
                <icon icon="chevron-left" fixed-width />
            </el-button>
        </div>

        <div class="block absolute right-0 z-20">
            <el-button class="py-3 shadow-md" circle type="primary" @click="slideNext()" :disabled="nextButtonDisabled">
                <icon icon="chevron-right" fixed-width />
            </el-button>
        </div>

        <swiper ref="nav" :options="navOptions">
            <swiper-slide
                v-for="n in slidesCount"
                :key="n">

                <div class="flex pt-2 pb-4">
                    <div class="flex-1 pr-8 text-center font-bold" v-for="round in winnerRounds.slice(n-1, offset+n)" :key="round.id">
                        {{ round.name }}
                    </div>
                </div>
            </swiper-slide>
        </swiper>
    </div>

    <swiper ref="swiper" :options="options" class="text-sm">
        <swiper-slide
            v-for="n in slidesCount"
            :key="n">

            <div class="flex">
                <brackets-round
                    v-for="(round, roundIndex) in winnerRounds.slice(n-1, offset+n)"
                    :key="round.id"
                    :is-draw="false"
                    :round="round"
                    :numbers-visible="n==1 && roundIndex==0">
                </brackets-round>
            </div>

            <div class="flex mt-12" v-if="loserRounds.length">
                <brackets-round
                    v-for="round in loserRounds.slice(n-1, offset+n)"
                    :key="round.id"
                    :is-draw="false"
                    :round="round"
                    :numbers-visible="false">
                </brackets-round>
            </div>
        </swiper-slide>
    </swiper>
</div>
</template>

<script>
import bracketsRound from '@/components/bracketsRound'

export default {
	components: {
        bracketsRound
    },

    props: {
        rounds: Array,
        width: Number
    },

    computed: {
        offset()
        {
            if (this.width < 500)
            {
                return 0
            }

            if (this.width < 900)
            {
                return 1
            }

            return 2
        },

        winnerRounds()
        {
            return this.rounds.filter(r => r.isWinner)
        },

        loserRounds()
        {
            return this.rounds.filter(r => !r.isWinner)
        },

        nextButtonDisabled()
        {
            return this.swiper ? this.swiper.isEnd : false
        },

        prevButtonDisabled()
        {
            return this.swiper ? this.swiper.isBeginning : false
        },

        slidesCount()
        {
            const winnersCount = this.rounds.filter(r => r.isWinner).length
            const losersCount = this.rounds.filter(r => !r.isWinner).length

            return Math.max(1, Math.max(winnersCount, losersCount) - this.offset)
        }
    },

    mounted()
    {
        this.$nextTick(() =>
        {
            const swiper = this.$refs.swiper.$swiper
            const swiperNav = this.$refs.nav.$swiper
            swiper.controller.control = swiperNav
            swiperNav.controller.control = swiper

            this.swiper = swiper
        })
    },

    methods: {
        slideNext()
        {
            this.$refs.swiper.$swiper.slideNext()
        },

        slidePrev()
        {
            this.$refs.swiper.$swiper.slidePrev()
        }
    },

	data()
	{
		return {
            swiper: null,
            navOptions: {
                slidesPerView: 1,
                allowTouchMove: false
            },

			options: {
                allowTouchMove: true,
                slidesPerView: 1,
                autoHeight: true,
                speed: 200
            }
		}
	}
}
</script>

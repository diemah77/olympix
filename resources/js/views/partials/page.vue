<template>
<div :class="[classes, {'mx-auto': centered}]">
    <header class="flex items-center mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl mr-3">{{ title }}</h1>

            <slot name="tag"></slot>
        </div>

        <div class="ml-auto">
            <slot name="buttons"></slot>
        </div>
    </header>

	<slot />
</div>
</template>

<script>
import Breadcrumbs from '@/components/breadcrumbs'

export default {
    components: {
		Breadcrumbs
    },

    props: {
        title: {
            type: String,
            require: true
        },

        width: {
            type: String,
            required: false,
            default: 'full',
            validator: function (value) {
                return ['full', 'narrow', 'default', 'max-xl'].indexOf(value) !== -1
            }
        },

        centered: {
            type: Boolean,
            default: true
        }
    },

    computed: {
        classes()
        {
            if (this.width == 'narrow')
            {
                return 'max-w-3xl'
            }

            if (this.width == 'default')
            {
                return 'max-w-5xl'
            }

            return this.width == 'full' ? 'max-w-none' : 'max-w-screen-xl'
        }
    }
}
</script>

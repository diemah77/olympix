<template>
<div :class="['mx-auto', classes]">
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
                return ['full', 'narrow', 'default'].indexOf(value) !== -1
            }
        }
    },

    computed: {
        classes()
        {
            if (this.width == 'narrow')
            {
                return 'max-w-3xl'
            }

            return this.width == 'default' ? 'max-w-5xl' : 'max-w-none'
        }
    }
}
</script>

<template>
<div class="h-screen overflow-hidden flex items-center justify-center">
    <div class="bg-gray-200 h-screen w-screen">
        <div class="flex flex-col items-center flex-1 h-full justify-center px-4 sm:px-0">
            <div class="flex rounded-lg shadow-lg w-full sm:w-3/4 lg:w-3/5 bg-white sm:mx-0" style="height: 500px">
                <div class="flex flex-col w-full md:w-2/5 p-4">
                    <div class="flex flex-col flex-1 justify-center mb-8">
                        <h1 class="flex text-4xl justify-center font-thin">
                            <img class="w-12 mr-2" src="/img/olympix.png">
                            <span>Olympix</span>
                        </h1>

                        <div class="w-full mt-4">
                            <form class="w-3/4 mx-auto" @submit.prevent="submit">
                                <div class="flex flex-col mt-4">
                                    <el-input v-model="form.email" placeholder="Email"></el-input>
                                </div>

                                <div class="flex flex-col mt-4">
                                    <el-input v-model="form.password" placeholder="Passwort" type="password"></el-input>
                                </div>
                                <div class="flex items-center mt-4">
                                    <el-checkbox v-model="form.remember" label="Remember me"></el-checkbox>
                                </div>

                                <div class="flex flex-col mt-8">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded">
                                        Login
                                    </button>
                                </div>
                            </form>
                            <div class="text-center mt-4">
                                <a class="no-underline hover:underline text-blue-600 text-xs" href="#">
                                    Passwort vergessen?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block md:w-3/5 rounded-r-lg" style="background: url('/img/bg.jpg'); background-size: cover; background-position: center center;"></div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    data()
    {
        return {
            form: {
                email: '',
                password: '',
                remember: ''
            }
        }
    },

    methods: {
        submit()
        {
            this.$inertia.post(route('login.attempt'), this.form).then(() => console.log('login'))
        }
    }
}
</script>

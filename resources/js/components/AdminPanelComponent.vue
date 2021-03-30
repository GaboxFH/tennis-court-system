<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Admin Panel</div>

                    <div class="card-body">
                        <p>I'm an example component.</p>

                        <div v-if="$can('View All Users')" >This text only is seen if you have the right persomissions.</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>

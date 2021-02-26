<template>
    <div>
        <div v-bind:key="reservation.id" v-for="reservation in foundReservations">
            <ReservationItem v-bind:reservation="reservation"/>
        </div>
    </div>    
</template>
<script>
import ReservationItem from './ReservationListItem';
export default {
    name: "ReservationList",
    components: {
        ReservationItem
    },
    props: ["foundReservations"]
}
</script>
<style scoped>

</style>
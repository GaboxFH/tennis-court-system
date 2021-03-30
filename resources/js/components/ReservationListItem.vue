<template>
    <div class="todo-item">
        <p>{{reservation.startTime}}</p>
    </div>
</template>

<script>
export default {
    name: "ReservationItem",
    props: ["reservation"]
}
</script>

<style scoped>
    .reserve-item {
        background: #f4f4f4;
        padding: 10px;
        border-bottom: 1px #ccc dotted;
    }

</style>
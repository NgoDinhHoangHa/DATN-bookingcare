<template>
    <div v-if="doctor?.dates?.length !== 0" class="order__item--right--calendar">
        <span @click="handleShowPopup()">{{ selected?.name }}</span>
        <span class="bx bx-chevron-down"></span>
        <ul :class="{ 'active': isShow }">
            <li @click="handleSelectedCalendar(calendar)" v-for="calendar in doctor.dates" :key="calendar.id">{{
                calendar.name
            }}</li>
        </ul>
    </div>
    <div>
        <i class='bx bxs-calendar'></i>
        <span>LỊCH KHÁM</span>
    </div>
    <div class="order__item--right--list-calendar" :class="doctor?.dates?.length !== 0 ? '' : 'disabled'">
        <div v-for="time in times.filter(time => !isPastTime(time))" :key="time.id"
            @click="clickItemTime(time, doctor, selected)" class="order__item--right--item" :class="[(() => {
                let index = [...doctor.books].findIndex(dt => dt.idtimebook === time.id &&
                    Number(dt.datebook.split('-')[1]) === selected.month && Number(dt.datebook.split('-')[2]) === selected.day);
                return index === -1 ? (time_ ? time_.id === time.id && book.date.id === selected.id ? 'active' : '' : '') : 'disabled'
            })()]">
            {{ time.time }}
        </div>
    </div>

    <p>Chọn <span class="bx bx-pointer"></span> và đặt (Phí đặt lịch 0đ)</p>
</template>
<script>
import { mapMutations, mapState } from 'vuex';

export default {
    data() {
        return {
            isShow: false,
            selected: this.doctor.dates.length > 0 ? this.doctor.dates[0] : {},
        };
    },
    props: ['times', 'doctor', 'isDetail', 'time_', 'disabled'],
    computed: {
        ...mapState(['book', 'user'])
    },
    methods: {
        ...mapMutations(["setBook"]),
        clickItemTime: function (time, doctor, date) {
            if (this.doctor?.dates?.length === 0) {
                return;
            }
            let index = [...doctor.books].findIndex(dt => dt.idtimebook === time.id &&
                Number(dt.datebook.split('-')[1]) === this.selected.month && Number(dt.datebook.split('-')[2]) === this.selected.day);
            if (index === -1) {
                if (time && !this.isDetail) {
                    this.setBook({
                        ...this.book,
                        time,
                        doctor,
                        date
                    })
                    this.$router.push({
                        name: "DoctorDetail", params: {
                            slug: this.$route.params.slug,
                            id: doctor.info.idadmin
                        }
                    })
                }
                else {
                    this.setBook({ ...this.book, time, date });
                }
            }
        },
        handleSelectedCalendar: function (calendar) {
            this.selected = calendar;
            this.isShow = false;
        },
        handleShowPopup: function () {this.isShow = !this.isShow;
        },
        isPastTime(timeObj) {
            const timeString = timeObj.time;
            const [startTimeStr] = timeString.split(' - ');
            const currentTime = new Date();
            const currentDay = currentTime.getDate();
            const selectedDay = this.selected?.day;
            if (selectedDay === currentDay) {
                const currentHour = currentTime.getHours();
                const currentMinute = currentTime.getMinutes();
                const currentFormattedTime = `${currentHour.toString().padStart(2, '0')}:${currentMinute.toString().padStart(2, '0')}`;

                return currentFormattedTime > startTimeStr;
            }
        }
    },
    mounted() {
        if (this.isDetail) {
            this.selected = this.doctor?.dates?.length > 0 ? this.doctor.dates[0] : '';
        }
    }
}
</script>
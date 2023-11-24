<template>
    <div v-if="hasPermission('create-meeting')"> <!-- Replace 'view_data' with your actual permission name -->
        <div class="m-10 text-end">
            <a href="#" class="btn btn-sm btn-primary" title="Add Meeting" @click="handleOpenModal"><i
                    class="ti ti-plus"></i></a>
        </div>
    </div>

    <Qalendar :events="events" :config="config" @date-was-clicked="handleDateClicked"
        @event-was-clicked="handleEventClicked">
        <template #eventDialog="props">
            <div v-if="props.eventDialogData && props.eventDialogData.title">
                <h3 :style="{ marginBottom: '8px' }" class="text-center mt-2">Meeting Details</h3>
                <div class="p-20">
                    <div class="mt-2">
                        <span class="font-bold">Title</span>:
                        <span>{{ props?.eventDialogData?.title }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="font-bold">Description</span>:
                        <span>{{ props?.eventDialogData?.description }}</span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <span class="font-bold">Assignee</span>:
                            <a-avatar-group :max-count="2" max-popover-trigger="click" size="large"
                                :max-style="{ color: '#f56a00', backgroundColor: '#fde3cf', cursor: 'pointer' }">
                                <template v-for="(value, index) in props.eventDialogData.assignee">
                                    <a-tooltip :title="value.name" placement="top">
                                        <a-avatar :style="{ backgroundColor: 'rgb(155 131 113)' }">{{ value.name.charAt(0)
                                        }}</a-avatar>
                                    </a-tooltip>
                                </template>
                            </a-avatar-group>
                        </div>
                        <div class="col-8 text-end align-self-end">
                            <span>Time</span>:
                            <span>{{ convertTo12HourFormat(props?.eventDialogData?.start_time) }}</span> to
                            <span>{{ convertTo12HourFormat(props?.eventDialogData?.end_time) }}</span>
                        </div>


                    </div>
                    <div class="mt-2">
                        <a-popconfirm title="Are you sure？" ok-text="Yes" cancel-text="No"
                            @confirm="handleCancelMeeting(props?.eventDialogData?.id)">
                            <a-button class="mt-2 w-100" danger>Cancel Meeting</a-button>
                        </a-popconfirm>
                    </div>
                </div>
            </div>
        </template>

        <template v-slot:event="{ event }">
            <div class="custom-event" :style="'background-color: ' + event.color">
                {{ event.title }}
            </div>
        </template>
    </Qalendar>

    <div>
        <a-modal v-model:open="open" width="22rem" title="Create Meeting" :footer="null">
            <div>
                <a-input v-model:value="form.title" placeholder="Enter Meeting Title" allow-clear class="mt-3" />
                <a-textarea v-model:value="form.description" placeholder="Enter Meeting Description" allow-clear
                    class="mt-2" />
                <br />

                <div class="mt-3">
                    <a-date-picker v-model:value="form.date" class="w-100" />
                </div>
                <div class="mt-3">
                    <a-time-picker v-model:value="form.time_in" format="HH:mm" placeholder="Time In" />
                    <a-time-picker v-model:value="form.time_out" format="HH:mm" placeholder="Time Out" class="m-l-20" />
                </div>

                <a-select v-model:value="form.assignee" mode="multiple" style="width: 100%" placeholder="Select Members"
                    max-tag-count="responsive" :options="users" class="mt-3"></a-select>

                <!--  -->
                <div class="custom-radios mt-3">

                    <div v-for="(color, index) in colors">
                        <input type="radio" v-model="form.color" :id="index" name="color" :value="index"
                            :checked="form.color === index">
                        <label for="color-1" @click="handleChangeColor(index)">
                            <span>
                                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/242518/check-icn.svg" class="m-t-10"
                                    alt="Checked Icon" />
                            </span>
                        </label>
                    </div>
                </div>
                <!--  -->

                <a-button type="primary" class="mt-2 w-100" @click="onSubmit">Create</a-button>
            </div>
        </a-modal>
    </div>
</template>

<script>
import { Qalendar } from "qalendar";
import { ref } from 'vue';
import moment, { now } from 'moment';
const users = [];
const open = ref(false);
const meetings = [];
const colors = {
    green: '#def5e6',
    blue: '#dff2f7',
    purple: '#d9d2ff',
    orange: '#feeed7',
    grey: '#e5e5e5'
}

export default {
    props: ['users', 'meetings'],
    components: {
        Qalendar,
    },
    methods: {
        handleDateClicked(value, e) {
            // this.form.meeting_date = value;
            // open.value = true;
        },
        handleEventClicked(v, e) {
        },
        showModal() {
            open.value = true;
        },
        onSubmit(e) {
            e.preventDefault();
            let currentObj = this;
            axios.post('/meeting/store', {
                title: this.form.title,
                description: this.form.description,
                time_in: this.form.time_in.format("hh:mm a"),
                time_out: this.form.time_out.format("hh:mm a"),
                assignee: this.form.assignee,
                color: this.form.color,
                meeting_date: this.form.meeting_date,
                date: this.form.date.format("YYYY-MM-DD"),
            }

            )
                .then(function (response) {
                    open.value = false;
                    location.reload();
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
        },

        handleChangeColor(color) {
            this.form.color = color;
        },

        convertTo12HourFormat(time24) {
            // Create a Date object with the 24-hour time
            const dateObj = new Date(`2000-01-01T${time24}`);

            // Use Intl.DateTimeFormat to format as 12-hour time
            const options = { hour: '2-digit', minute: '2-digit', hour12: true };
            return new Intl.DateTimeFormat('en-US', options).format(dateObj);
        },
        handleOpenModal() {
            open.value = true;
        },
        handleCancelMeeting(meetingId) {
            axios.post('/meeting/cancel', {
                id: meetingId,
            }
            )
                .then(function (response) {
                    open.value = false;
                    location.reload();
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            console.log(meetingId, 'meetingId');
        },
        hasPermission(permission) {
            // Get user permissions from the meta tag
            const userPermissions = JSON.parse(document.querySelector('meta[name="user-permissions"]').content);

            // Check if the user has the specified permission
            return userPermissions.includes(permission);
        }

    },

    data(props) {
        props.users.forEach(element => {
            users.push({
                label: element.name,
                value: element.id,
            });
        });
        props.meetings.forEach(element => {
            meetings.push(element);
        });
        return {
            events: meetings,
            config: {
                dayBoundaries: {
                    start: 6,
                    end: 18,
                },
                // see configuration section
                eventDialog: {
                    isCustom: true
                },
                style: {
                    colorSchemes: {
                        green: {
                            color: 'black',
                            // backgroundColor: '#cffad6',
                            backgroundColor: '#def5e6',
                        },
                        purple: {
                            color: 'black',
                            backgroundColor: '#d9d2ff',
                        },
                        blue: {
                            color: 'black',
                            backgroundColor: '#dff2f7',
                        },
                        orange: {
                            color: 'black',
                            backgroundColor: '#feeed7',
                        },
                        grey: {
                            color: 'black',
                            backgroundColor: '#e5e5e5',
                        },
                    }
                },
                // defaultMode: 'month',
            },
            visible: false,
            form: {
                title: '',
                description: '',
                time_in: null,
                time_out: null,
                assignee: [],
                color: 'green',
                meeting_date: null,
                date: null
            },
            open,
            moment,
            colors,
            users,
            meetings: meetings,

        }
    },

    mounted: function () {
        (function () {
            setTimeout(() => {
                const eventElements = document.querySelectorAll('.calendar-month__event');
                console.log(eventElements);
                eventElements.forEach((eventElement) => {

                    console.log(getComputedStyle(eventElement.firstElementChild).backgroundColor, 'style');
                    eventElement.style.backgroundColor = getComputedStyle(eventElement.firstElementChild).backgroundColor;
                    eventElement.addEventListener('click', (event) => {
                        event.stopPropagation();
                    });
                });
            }, 1000);
        })();
    }
}
</script>

<style>
.week-timeline__event {
    height: 2rem !important;
}

.calendar-month__event {
    /* background: bisque !important; */
    height: 2rem !important;
}

.custom-event {
    color: white;
    /* Text color for the event */
    padding: 4px;
    border-radius: 4px;
    text-align: center;
    cursor: pointer;
}

.mode-is-month {
    height: 40rem !important;
}

.ant-modal-mask {
    height: 0px !important;
}

/*  */
.custom-radios div {
    display: inline-block;
}

.custom-radios input[type="radio"] {
    display: none;
}

.custom-radios input[type="radio"]+label {
    color: #333;
    font-family: Arial, sans-serif;
    font-size: 14px;
}

.custom-radios input[type="radio"]+label span {
    display: inline-block;
    width: 40px;
    height: 40px;
    margin: -1px 4px 0 0;
    vertical-align: middle;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
    background-repeat: no-repeat;
    background-position: center;
    text-align: center;
    line-height: 44px;
}

.custom-radios input[type="radio"]+label span img {
    opacity: 0;
    transition: all 0.3s ease;
}

.custom-radios input[type="radio"]#green+label span {
    background-color: #def5e6;
}

.custom-radios input[type="radio"]#blue+label span {
    background-color: #dff2f7;
}

.custom-radios input[type="radio"]#purple+label span {
    background-color: #d9d2ff;
}

.custom-radios input[type="radio"]#orange+label span {
    background-color: #feeed7;
}

.custom-radios input[type="radio"]#grey+label span {
    background-color: #e5e5e5;
}

.custom-radios input[type="radio"]:checked+label span img {
    opacity: 1;
}
</style>

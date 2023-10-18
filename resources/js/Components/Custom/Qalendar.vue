<template>
    <Qalendar  :events="events" :config="config" @date-was-clicked="handleDateClicked">
        <template #eventDialog="props">
            <div v-if="props.eventDialogData && props.eventDialogData.title">
                <h3 :style="{ marginBottom: '8px' }" class="text-center mt-2">Meeting Details</h3>
                <div class="p-20">
                    <div class="mt-2">
                        <span class="font-bold">Title</span>:
                        <span>Staff Meeting</span>
                    </div>
                    <div class="mt-2">
                        <span class="font-bold">Description</span>:
                        <span>meeting for managing I.C.U </span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <span class="font-bold">Assignee</span>:
                            <!-- <a-avatar style="background-color: #1890ff">d
                </a-avatar> -->

                            <a-avatar-group :max-count="2" max-popover-trigger="click" size="large"
                                :max-style="{ color: '#f56a00', backgroundColor: '#fde3cf', cursor: 'pointer' }">
                                <a-tooltip title="dummyuser" placement="top">
                                    <a-avatar src="https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png" />
                                </a-tooltip>
                                <a-tooltip title="dummyuser" placement="top">
                                    <a-avatar style="background-color: #f56a00">K</a-avatar>
                                </a-tooltip>
                                <a-tooltip title="dummyuser" placement="top">
                                    <a-avatar style="background-color: #87d068">
                                        <template #icon>
                                            <UserOutlined />
                                        </template>
                                    </a-avatar>
                                </a-tooltip>
                            </a-avatar-group>
                        </div>
                        <div class="col-6 text-end align-self-end">
                            <span>Time</span>:
                            <span>6:00 pm</span>
                        </div>


                    </div>
                </div>


                <!-- <input class="flyout-input" type="text" style="width: 90%, padding: 8px, marginBottom: 8px" >

        <button class="close-flyout" @click="props.closeEventDialog">
          Finished!
        </button> -->
            </div>
        </template>

        <template v-slot:event="{ event }">
            <div class="custom-event" :style="'background-color: ' + event.color">
                {{ event.title }}
            </div>
        </template>
    </Qalendar>

    <!-- <div> -->
        <!-- <a-button type="primary" @click="showModal">Open Modal</a-button>
        <a-button @click="success">Success</a-button> -->
        <!-- <a-modal v-model:open="open" title="Basic Modal">
            </a-modal> -->
    <!-- </div> -->
</template>

<script>
import { Qalendar } from "qalendar";
import { Avatar } from "./Avatar";
import { Modal } from 'ant-design-vue';
import { ref } from 'vue';
const open = ref(false);
export default {
    components: {
        Qalendar,
        Avatar,
       
    },
    methods: {
        // handleDateClicked(value) {
        //     alert(value);
        //     open.value = true;
        // },
        showModal() {
            open.value = true;
        },
        handleOk(e) {
            console.log(e);
            open.value = false;
        },
        success() {
            Modal.success({
                title: 'This is a success message',
            });
        }
    },

    data() {
        return {
            events: [
                // ...
                {
                    title: "Meeting For Health",
                    // with: "Chandler Bing",
                    time: { start: "2023-10-18 07:05", end: "2023-10-18 09:00" },
                    color: "green",
                    // isEditable: true,
                    id: "753944708f0f",
                    // description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Meeting For Work",
                    // with: "Chandler Bing",
                    time: { start: "2023-10-21 08:05", end: "2023-10-21 11:00" },
                    color: "red",
                    // isEditable: true,
                    id: "7539448f0f",
                    // description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Meeting For Food",
                    // with: "Chandler Bing",
                    time: { start: "2023-10-20 11:05", end: "2023-10-20 13:00" },
                    color: "yellow",
                    // isEditable: true,
                    id: "753944f0f",
                    // description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Meeting For Sport",
                    // with: "Chandler Bing",
                    time: { start: "2023-10-21 11:05", end: "2023-10-21 13:00" },
                    color: "brown",
                    // isEditable: true,
                    id: "7539f0f",
                    // description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Meeting For Food",
                    // with: "Chandler Bing",
                    time: { start: "2023-10-16 11:05", end: "2023-10-16 13:00" },
                    color: "blue",
                    // isEditable: true,
                    id: "753944f0f",
                    // description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
            ],
            config: {
                height:'2000px',
                eventStyle: {
                    background: this.eventBackgroundColor, // Bind background color
                },
                dayBoundaries: {
                    start: 6,
                    end: 18,
                },
                // see configuration section
                eventDialog: {
                    isCustom: true
                },
                style: {
                    // When adding a custom font, please also set the fallback(s) yourself
                    fontFamily: '',
                },
            },
            visible: false,
            form: {
                title: '',
                description: '',
                time: null,
                assignee: '',
            },
            open,
            eventBackgroundColor: 'yellow',
        }
    },
}
</script>

<style>
/* @import "qalendar/dist/style.css"; */

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

.mode-is-month{
    height: 40rem !important;
}
</style>

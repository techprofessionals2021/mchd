<template>
    <Qalendar :events="events" :config="config">
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
    </Qalendar>
</template>

<script>
import { Qalendar } from "qalendar";
import { Avatar } from "./Avatar";

export default {
    components: {
        Qalendar,
        Avatar
    },

    data() {
        return {
            events: [
                // ...
                {
                    title: "Advanced algebra",
                    with: "Chandler Bing",
                    time: { start: "2023-10-17 12:05", end: "2023-10-20 13:35" },
                    color: "yellow",
                    isEditable: true,
                    id: "753944708f0f",
                    description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Ralph on holiday",
                    with: "Rachel Greene",
                    time: { start: "2022-05-10", end: "2022-05-22" },
                    color: "green",
                    isEditable: true,
                    id: "5602b6f589fc"
                }
                // ...
            ],
            config: {
                // see configuration section
                eventDialog: {
                    isCustom: true
                },
                style: {
                    // When adding a custom font, please also set the fallback(s) yourself
                    fontFamily: '',

                },
        }
    }
},
}
</script>

<style>
/* @import "qalendar/dist/style.css"; */

.week-timeline__event{
    height: 2rem !important;
}

.calendar-month__event{
    background: bisque !important;
    height: 2rem !important;
}
</style>

<template>
    <a-menu v-model:selectedKeys="current" mode="horizontal" :items="items"  />
</template>
<script>
import { h, ref } from 'vue';
import { MailOutlined, AppstoreOutlined, SettingOutlined, UnorderedListOutlined, CalendarOutlined, TableOutlined } from '@ant-design/icons-vue';


let url = window.location.href;
let array = ["calender", "gantt",'board'];

let current = ref(['list']);

for (let tab of array) {
  if (url.includes(tab)) {
    current = ref([tab]);;
    break; // Exit the loop as soon as a match is found
  }
}


export default {
    props: ['routes'],
    setup(props) {
        console.log(props.routes, 'props');
        const items = ref([
            {
                key: 'list',
                icon: () => h(UnorderedListOutlined),
                label: h(
                    'a',
                    {
                        href: props.routes.list,
                    },
                    'List',
                ),
                title: 'Navigation One',
            },
            {
                key: 'board',
                icon: () => h(
                    'object',
                    {
                        data: window.location.origin + '/custom-ui/images/Vector.svg',
                        height: "12"
                    }
                ),
                label: h(
                    'a',
                    {
                        href: props.routes.board,
                    },
                    'Board',
                ),
                title: 'Board',
            },
            {
                key: 'gantt',
                icon: () => h(
                    'object',
                    {
                        data: window.location.origin + '/custom-ui/images/gantt.svg',
                        height: "12"
                    }
                ),
                label: h(
                    'a',
                    {
                        href: '',
                    },
                    'Gantt',
                ),
                title: 'Gantt',
            },
            {
                key: 'calender',
                icon: () => h(CalendarOutlined),
                label: h(
                    'a',
                    {
                        href: props.routes.calender,
                    },
                    'Calender',
                ),
                title: 'Calender',
            },
        ]);


        return {
            items,
            current,
            props,
        }
    }
}
</script>

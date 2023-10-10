<template>
    <a-menu v-model:selectedKeys="current" mode="horizontal" :items="items" />
</template>
<script>
import { h, ref } from 'vue';
import { MailOutlined, AppstoreOutlined, SettingOutlined,UnorderedListOutlined,CalendarOutlined,TableOutlined } from '@ant-design/icons-vue';
const current = ref(['mail']);
const items = ref([
    {
        key: 'mail',
        icon: () => h(UnorderedListOutlined),
        label: h(
            'a',
            {
                href: '',
                target: '_blank',
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
                height:"12"
            }
        ),
        label: h(
            'a',
            {
                href: '',
                target: '_blank',
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
                height:"12"
            }
        ),
        label: h(
            'a',
            {
                href: '',
                target: '_blank',
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
                href: '',
                target: '_blank',
            },
            'Calender',
        ),
        title: 'Calender',
    },
]);

export default {
    setup(props) {
        console.log('menu');
        return {
            items,
            current
        }
    }
}
</script>

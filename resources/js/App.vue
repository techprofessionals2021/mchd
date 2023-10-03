


<!--  -->

<template>
    <a-table :columns="columns" :data-source="data" :row-selection="rowSelection" >

    <template #bodyCell="{ column, text,record }">

      <template v-if="column.dataIndex === 'title'">

            <a v-if="record['modal_url']" href="#" data-size="lg" :data-url="record['modal_url']" data-ajax-popup="true" data-title="Task Detail" class="h6 task-title"><h5>{{text}}</h5></a>
            <!-- <h1 v-if="awesome">Vue is awesome!</h1> -->
            <a v-else>{{text}}</a>
      </template>

      <template v-else-if="column.dataIndex === 'priority'">
        <span>
          <a-tag
            :color="text === 'Low' ? 'volcano' : (text === 'Medium' ? 'green': 'red')"
          >
            {{ text.toUpperCase() }}
          </a-tag>
        </span>
      </template>
    </template>

    </a-table>
</template>
<script>
import { ref } from 'vue';
const columns = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
    },
    {
      title: 'Title',
      dataIndex: 'title',
      key: 'title',
    //   width: '12%',
    },
    {
      title: 'Due Date',
      dataIndex: 'due_date',
      key: 'due_date',
    //   width: '12%',
    },
    {
      title: 'Priority',
      dataIndex: 'priority',
      key: 'priority',
    //   width: '12%',
    },
    // {
    //   title: 'Title',
    //   dataIndex: 'title',
    //   key: 'title',
    // //   width: '12%',
    // },
];

const rowSelection = ref({
    checkStrictly: false,
    onChange: (selectedRowKeys, selectedRows) => {
      console.log(`selectedRowKeys: ${selectedRowKeys}`, 'selectedRows: ', selectedRows);
    },
    onSelect: (record, selected, selectedRows) => {
      console.log(record, selected, selectedRows);
    },
    onSelectAll: (selected, selectedRows, changeRows) => {
      console.log(selected, selectedRows, changeRows);
    },
});


export default {
    props: ['tasks'],
    setup(props) {
        console.log(props.tasks)
        return {
            columns,
            data:props.tasks,
            rowSelection
        }
    }
}
</script>

<!-- <script setup>
// defineProps(['project'])
const props = defineProps(['project'])
console.log(props.project,'project')
</script> -->

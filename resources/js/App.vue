


<!--  -->

<template >
    <a-table :columns="columns" :data-source="data" :customHeaderRow="(columns, index) => {
        console.log(columns, 'columns');
    }">


       <template>
        <div>
            <!-- Your select element and other components -->

            <!-- Display a message if status is updated successfully -->
            <div v-if="updateSuccessMessage" class="success-message">
            Status Updated: {{ updateSuccessMessage }}
            </div>
        </div>
        </template>


        <template #headerCell="{ column }">
            <template v-if="column.key === 'title'">
                <span>
                    <span class="id-label">
                      Title
                    </span>
                    <span class="m-l-5 text-common">
                        {{ data.length }} Tasks
                    </span>
                </span>
            </template>
        </template>



 
        <template #bodyCell="{ column, text, record }">



            <template v-if="column.dataIndex === 'title'">

                <a v-if="record['modal_url']" href="#" data-size="lg" :data-url="record['modal_url']" data-ajax-popup="true"
                    data-title="Task Detail" class="h6 task-title">
                    <h5>{{ text }}</h5>
                </a>
                <a v-else>{{ text }}</a>
            </template>


            <template v-else-if="column.dataIndex === 'assignee'">
                <a-avatar-group :max-count="2" size="large" :max-style="{
                    color: '#f56a00',
                    backgroundColor: '#fde3cf',
                }">
                    <a-tooltip :title="assign.name" placement="top" v-for="assign in record.assignee">
                        <a-avatar style="background-color: #1890ff">{{ assign.name.charAt(0) }}
                        </a-avatar>
                    </a-tooltip>
                </a-avatar-group>
            </template>

            <!-- <template v-else-if="column.dataIndex === 'status'">
                <a-tag :color=text?.color>{{ text?.name }}</a-tag>
               
            </template> -->
<!-- 
            <template v-else-if="column.dataIndex === 'status'">
            <div>
                <a-select v-model:value="selectedStatus" style="width: 120px"  @change="updateStatus" >
                <a-select-option v-for="status in record['all_status']" :key="status.id" :value="status.id">
                    <a-tag :color="status.color">{{ status.name }}</a-tag>
                </a-select-option>
                </a-select>
            </div>
            </template> -->

            <template v-else-if="column.dataIndex === 'status'">
            <div>
                <a-select v-if="record.status"  v-model:value="record.selectedStatus" style="width: 120px" @change="updateStatus(record,$event)">
                <a-select-option v-for="status in record['all_status']" :key="status.id" :value="status.id">
                    <a-tag :color="status.color"> {{ status.name }} </a-tag>
                </a-select-option>
                </a-select>
            </div>
            </template>

            <template v-else-if="column.dataIndex === 'due_date'">
                {{ dateFormatter(text) }}
            </template>

            <template v-else-if="column.dataIndex === 'priority'">
                <!-- <FlagOutlined :style="{ color: text == 'Medium' ? 'red' : text == 'Low' ? 'green' : '' }" /> -->
                <a-tooltip :title="text" placement="top">
                    <!-- <a-avatar style="background-color: #87d068"> -->
                    <!-- <template> -->
                    <FlagOutlined :style="{ color: text == 'High' ? 'red' : text == 'Low' ? 'green' : text == 'Medium' ? 'brown' : '' }" />
                    <!-- </template> -->
                    <!-- </a-avatar> -->
                </a-tooltip>
            </template>


            <template v-else-if="column.dataIndex === 'edittask'">
            <a v-if="record['modal_url_edit'] && record['workspace_permissions'] && record['workspace_permissions'].includes('edit task')" href="#" data-size="lg" :data-url="record['modal_url_edit']" data-ajax-popup="true" data-title="Task Edit" class="h6 task-title">
                <h5><EditTwoTone style="font-size: 24px;" /></h5>
            </a>

            <a v-else-if="Array.isArray(record['permission']) && record['permission'].includes('Owner')" href="#" data-size="lg" :data-url="record['modal_url_edit']" data-ajax-popup="true" data-title="Task Edit" class="h6 task-title">
                <h5><EditTwoTone style="font-size: 24px;" /></h5>
            </a>
            <a v-else-if="record['workspace_permissions'] === null" href="#">
                <!-- <h5><EditTwoTone style="font-size: 24px;" /></h5> -->
            </a>
        </template>

        <template v-else-if="column.dataIndex === 'deletetask'">
            <a v-if="record['workspace_permissions'] && record['workspace_permissions'].includes('delete task')" :href="record['modal_url_destory']" class="h6">
                <h5><DeleteTwoTone  style="font-size: 24px;" /></h5>
            </a>
            <a v-else-if="Array.isArray(record['permission']) && record['permission'].includes('Owner')"  :href="record['modal_url_destory']" class="h6">
                <h5><DeleteTwoTone  style="font-size: 24px;" /></h5>
           </a>
            <a v-else-if="record['workspace_permissions'] === null" href="#">
                <!-- <h5><DeleteTwoTone  style="font-size: 24px;" /></h5> -->
            </a>
        </template>

        </template>

    </a-table>
</template>
<script>
import { ref } from 'vue';
import {

    CalendarOutlined,
    TableOutlined,
    FlagOutlined,
    EditTwoTone,
    DeleteTwoTone ,
} from '@ant-design/icons-vue';
import moment from 'moment';

// const workspacePermissions = ['edit task'];

    // const workspacePermissions = this.record['workspace_permissions'];



// const columns = [
//   {
//     title: 'Title',
//     dataIndex: 'title',
//     key: 'title',
//   },
//   {
//     title: 'Assignee',
//     dataIndex: 'assignee',
//     key: 'assignee',
//   },
//   {
//     title: 'Status',
//     dataIndex: 'status',
//     key: 'status',
//   },
//   {
//     title: 'DUE DATE',
//     dataIndex: 'due_date',
//     key: 'due_date',
//   },
//   {
//     title: 'Priority',
//     dataIndex: 'priority',
//     key: 'priority',
//   },
//   ...(workspacePermissions.includes('edit task')
//     ? [
//         {
//           title: 'Edit Task',
//           dataIndex: 'edittask',
//           key: 'edittask',
//         },
//       ]
//     : []
//   ),
//   ...(workspacePermissions.includes('delete task')
//     ? [
//         {
//           title: 'Delete Task',
//           dataIndex: 'deletetask',
//           key: 'deletetask',
//         },
//       ]
//     : []
//   ),
// ];


const columns = [
  {
    title: 'Title',
    dataIndex: 'title',
    key: 'title',
  },
  {
    title: 'Assignee',
    dataIndex: 'assignee',
    key: 'assignee',
  },
  {
    title: 'Status',
    dataIndex: 'status',
    key: 'status',
  },
  {
    title: 'DUE DATE',
    dataIndex: 'due_date',
    key: 'due_date',
  },
  {
    title: 'Priority',
    dataIndex: 'priority',
    key: 'priority',
  },
  
     {
          title: 'Edit Task',
          dataIndex: 'edittask',
          key: 'edittask',
      },
  
  
        {
          title: 'Delete Task',
          dataIndex: 'deletetask',
          key: 'deletetask',
        },
   
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


import { message } from 'ant-design-vue';


export default {
    props: ['tasks'],
    setup(props) {

        console.log(props,'props');


        const selectedStatus = ref(null);
        

        
        return {
            columns,
            data: props.tasks,
            rowSelection,
            selectedStatus,
            updateSuccessMessage: '', 

        }
    },
    components: {
        CalendarOutlined,
        FlagOutlined,
        EditTwoTone,
        DeleteTwoTone
    },
    methods: {
        dateFormatter(dateTimeString) {
            // console.log(dateTimeString);
            // Parse the date-time string using Moment.js
            const parsedDate = moment(dateTimeString, "YYYY-MM-DD HH:mm:ss");

            // Format the date in the desired format
            const formattedDate = parsedDate.format("MMM, DD YYYY");
            return formattedDate;
        },

        updateStatus(record, selectedStatus) {
            
            if (selectedStatus) {

                const taskUpdateUrl = record['url_update_task_status'];
                const id = record['id'];
                const old_status = record['old_status'];
                const new_status = selectedStatus;
                const project_id = record['project_id'];
       
           
 
            axios.post(taskUpdateUrl, {
                id: id,
                new_status: new_status,
                old_status: old_status,
                project_id: project_id,
            })
            .then(response => {
         
            message.success('Status Updated Successfully');
            })
            .catch(error => {
            console.error(error);
            });
        }

        }
    },

    mounted() {

        if (this.tasks.length > 0) {
      // Loop through each task and set the selectedStatus using ref
        this.tasks.forEach(task => {
            // Assuming you want to set the default value to the old status of each task
            task.selectedStatus = ref(task.old_status);
        });
        } else {
        // Provide a default value if tasks is empty
        this.selectedStatus.value = null; // or any other default value you prefer
        }

    },


}
</script>


<style>
.id-label {
    background-color: #014AAB;
    color: white;
    border-radius: 5px 5px 0px 0px;
    padding: 1px 0px 0px 5px;
}


.success-message {
  color: green;
  margin-top: 10px;
}

.ant-table-wrapper .ant-table-thead>tr>th {
    background: #FFFFFF;
    font-family: 'Montserrat';
    font-size: 14px;
    /* font-weight: 500; */
    line-height: 24px;
    letter-spacing: 0em;
    color: #A6A5A5 !important;
}

.ant-table-row>td , .ant-table-row>td>a ,.ant-table-row>td>a>h5{
    /* background: #FFFFFF; */
    font-family: 'Montserrat';
    font-size: 14px;
    /* font-weight: 500; */
    line-height: 24px;
    letter-spacing: 0em;
    color: #A6A5A5 !important;
}
</style>

<template>
  <a-space direction="vertical" :size="32">
    <a-space wrap :size="16">
      <a-avatar shape="square" :size="46" style="background-color: #014AAB; ">
        <template #icon><UserOutlined style="margin:10px" /></template>
      </a-avatar>
    </a-space>
  </a-space>
  </template>
  <script>
    import { UserOutlined } from "@ant-design/icons-vue";
  export default {
    setup(){

    },
    components:{
        UserOutlined
    }

  };
  </script>

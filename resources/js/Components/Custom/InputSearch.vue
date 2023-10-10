<template>
    <a-space direction="vertical">
      <a-input-search
        v-model:value="value"
        placeholder="input search text"
        style="width: 200px"
        @search="onSearch"
      />
    </a-space>
  </template>
  <script>
  import { ref } from 'vue';
  const value = ref('');
  const onSearch = searchValue => {
    console.log('use value', searchValue);
    console.log('or use this.value', value.value);
  };
  export default {

  }
  </script>

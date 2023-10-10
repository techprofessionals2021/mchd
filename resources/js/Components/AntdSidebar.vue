<!-- <template>
    <a-menu
      id="dddddd"
      v-model:openKeys="openKeys"
      v-model:selectedKeys="selectedKeys"
      style="width: 256px"
      mode="inline"
      :items="items"
      @click="handleClick"
    ></a-menu>
  </template>
  <script>
  import { reactive, ref, watch, h } from 'vue';
  import { MailOutlined, AppstoreOutlined, SettingOutlined } from '@ant-design/icons-vue';
  const selectedKeys = ref(['1']);
  const openKeys = ref(['sub1']);
  function getItem(label, key, icon, children, type) {
    return {
      key,
      icon,
      children,
      label,
      type,
    };
  }
  const items = reactive([
    getItem('Navigation One', 'sub1', () => h(MailOutlined), [
      getItem('Item 1', 'g1', null, [getItem('Option 1', '1'), getItem('Option 2', '2')], 'group'),
      getItem('Item 2', 'g2', null, [getItem('Option 3', '3'), getItem('Option 4', '4')], 'group'),
    ]),
    getItem('Navigation Two', 'sub2', () => h(AppstoreOutlined), [
      getItem('Option 5', '5'),
      getItem('Option 6', '6'),
      getItem('Submenu', 'sub3', null, [getItem('Option 7', '7'), getItem('Option 8', '8')]),
    ]),
    {
      type: 'divider',
    },
    getItem('Navigation Three', 'sub4', () => h(SettingOutlined), [
      getItem('Option 9', '9'),
      getItem('Option 10', '10'),
      getItem('Option 11', '11'),
      getItem('Option 12', '12'),
    ]),
    getItem('Group', 'grp', null, [getItem('Option 13', '13'), getItem('Option 14', '14')], 'group'),
  ]);
  const handleClick = e => {
    console.log('click', e);
  };
  watch(openKeys, val => {
    console.log('openKeys', val);
  });

  export default {
    // props: ['tasks'],
    setup(props) {
        // console.log(props.tasks)
        return {
            openKeys,
            selectedKeys,
            items,
            handleClick
        }
    }
}
  </script> -->

<template>
    <a-menu v-model:selectedKeys="selectedKeys" mode="inline">
        <a-menu-item key="1">
            <pie-chart-outlined />
            <span>Dashboard</span>
        </a-menu-item>
        <!-- <a-menu-item key="2">
      <desktop-outlined />
      <span>Option 2</span>
    </a-menu-item> -->
        <a-sub-menu key="sub1">
            <template #title>
                <span>
                    <user-outlined />
                    <span>Space</span>
                </span>
            </template>
            <a-menu-item key="2">
                <!-- <template> -->
                <!-- <a-space wrap> -->
                <a-button type="dashed" >
                    <template #icon>
                        <PlusOutlined :style="{display:'inline-flex'}" />
                    </template>
                    New Space
                </a-button>
                <!-- </a-space> -->
                <!-- </template> -->

            </a-menu-item>
            <a-menu-item key="3">Tom</a-menu-item>
            <a-menu-item key="4">Bill</a-menu-item>
            <a-menu-item key="5">Alex</a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="sub2">
            <template #title>
                <span>
                    <team-outlined />
                    <span>Team</span>
                </span>
            </template>
            <a-menu-item key="6">Team 1</a-menu-item>
            <a-menu-item key="8">Team 2</a-menu-item>
        </a-sub-menu>
        <a-menu-item key="9">
            <file-outlined />
            <span>File</span>
        </a-menu-item>
    </a-menu>
</template>
<script>
import { ref } from 'vue';
import { DownloadOutlined,PlusOutlined } from '@ant-design/icons-vue';
const collapsed = ref(false);
const selectedKeys = ref(['1']);
export default {
    // props: ['tasks'],
    setup(props) {
        // console.log(props.tasks)
        return {
            collapsed,
            selectedKeys,
            DownloadOutlined,

        }
    },
    components: {
        PlusOutlined
    },
}
</script>

<style>
.ant-btn {
    font-size: 14px;
    height: 33px;
    padding: 4px 45px;
    border-radius: 6px;
}
</style>

<!-- <style scoped>
#components-layout-demo-side .logo {
  height: 32px;
  margin: 16px;
  background: rgba(255, 255, 255, 0.3);
}

.site-layout .site-layout-background {
  background: #fff;
}
[data-theme='dark'] .site-layout .site-layout-background {
  background: #141414;
}
</style> -->

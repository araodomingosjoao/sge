<template>
    <div class="app-menu navbar-menu">
        <div id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li v-for="item in menuItems" :key="item.label" class="nav-item">
                        <router-link class="nav-link menu-link" :to="item.link || '#'"
                            :data-bs-toggle="item.submenu ? 'collapse' : ''" role="button" aria-expanded="false">
                            <i :class="item.icon"></i> <span>{{ item.label }}</span>
                    </router-link>
                        <div v-if="item.submenu" class="collapse menu-dropdown">
                            <ul class="nav nav-sm flex-column">
                                <li v-for="subItem in item.submenu" :key="subItem.label" class="nav-item">
                                    <router-link :to="subItem.link" class="nav-link">{{ subItem.label }}</router-link>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>

import menuItems from '../../config/menuItems.js';

export default {
    name: 'Navbar',
    data() {
        return {
            menuItems
        };
    }
};

</script>
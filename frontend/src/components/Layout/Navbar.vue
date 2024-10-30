<template>
    <div class="app-menu navbar-menu">
        <div id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li v-for="item in menuItems" :key="item.label" class="nav-item">
                        <router-link class="nav-link menu-link" :to="item.link || '#'"
                            :data-bs-toggle="item.submenu ? 'collapse' : ''" role="button"
                            aria-expanded="!!item.submenu" :aria-controls="item.submenu ? item.id : null"
                            :href="item.link ? undefined : `#${item.id}`">
                            <i :class="item.icon"></i> <span>{{ item.label }}</span>
                        </router-link>

                        <!-- Submenu -->
                        <div v-if="item.submenu" class="collapse menu-dropdown" :id="item.id">
                            <ul class="nav nav-sm flex-column">
                                <li v-for="subItem in item.submenu" :key="subItem.label" class="nav-item">
                                    <router-link :to="subItem.link || '#'" class="nav-link"
                                        :data-bs-toggle="subItem.submenu ? 'collapse' : ''" role="button"
                                        aria-expanded="!!subItem.submenu"
                                        :aria-controls="subItem.submenu ? subItem.id : null"
                                        :href="subItem.link ? undefined : `#${subItem.id}`">
                                        {{ subItem.label }}
                                    </router-link>

                                    <!-- Submenu de Submenu -->
                                    <div v-if="subItem.submenu" class="collapse submenu-dropdown" :id="subItem.id">
                                        <ul class="nav nav-sm flex-column">
                                            <li v-for="subSubItem in subItem.submenu" :key="subSubItem.label"
                                                class="nav-item">
                                                <router-link :to="subSubItem.link || '#'" class="nav-link"
                                                    data-key="t-level-1.2">
                                                    <i class="bx bxs-checkbox-minus"></i>
                                                    {{ subSubItem.label }}
                                                </router-link>
                                            </li>
                                        </ul>
                                    </div>
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
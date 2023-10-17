<template>
    <div>
        <input type="text" v-model="searchQuery" placeholder="Поиск по названию или номеру...">
        <table style="width:100%;border:0;text-align:center" class="luchie">
            <tbody>
            <tr>
                <td class="contentdescription" colspan="2"></td>
            </tr>
            <tr>
                <td>
                    <table class="display" id="cattable">
                        <thead>
                        <tr>
                            <th class="sectiontableheader" style="text-align:right;width:5%">Num</th>
                            <th class="sectiontableheader" style="text-align:left;">Название</th>
                            <th class="sectiontableheader" style="text-align:left;">Рейтинг</th>
                            <th class="sectiontableheader" style="text-align:left;">Отзывов о садике</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(group, groupName) in groupedItems" :key="groupName">
                            <tr class="group">
                                <td></td>
                                <td colspan="3"><h3>{{ groupName }}</h3></td>
                            </tr>
                            <tr v-for="(item, index) in filteredItems(group)" :key="item.id" :class="{
                    'sectiontableentry even': index % 2 === 0,
                    'sectiontableentry odd': index % 2 !== 0
                  }" v-if="filteredItems(group).length > 0 || !hasDataInOkrugField">
                                <td data-label="№">{{ index + 1 }}</td>
                                <td v-if="filteredItems(group).length === 0 && hasDataInOkrugField">&nbsp;</td>
                                <td v-else><a :href="item.link">{{ item.name }}</a></td>
                                <td data-label="Рейтинг">{{ parseFloat(item.average).toFixed(1) }}</td>
                                <td data-label="Отзывов о садике">{{ item.comments }}</td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            items: window.items,
            searchQuery: '',
        };
    },
    computed: {
        groupedItems() {
            const grouped = {};

            this.items.forEach((item) => {
                const groupName = item.okrug || '';
                if (!grouped[groupName]) {
                    grouped[groupName] = [];
                }
                grouped[groupName].push(item);
            });
            return grouped;
        },
        hasDataInOkrugField() {
            return this.items.some(item => !!item.okrug);
        },
    },
    methods: {
        filteredItems(group) {
            const searchQuery = this.searchQuery.toLowerCase().trim();

            return group.filter(item => {
                return (
                    item.name.toLowerCase().includes(searchQuery) ||
                    item.id.toString().includes(searchQuery)
                );
            });
        },
    },
};
</script>

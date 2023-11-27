<template>
    <div v-if="!loading" class="dashboard__content">
        <p>{{ title }}</p>
        <table>
            <tr v-if="hideCrud">
                <th>STT</th>
                <th v-for="h in heading" :key="h">{{ h }}</th>
            </tr>
            <tr v-else>
                <th>STT</th>
                <th v-for="h in heading" :key="h">{{ h }}</th>
                <th>Sửa</th>
                <th>Xoá</th>
            </tr>
            <template v-if="!loading && list?.length > 0">
                <slot></slot>
            </template>
            <template v-else>
                <tr>
                    <td colspan="10" style="border: none;">
                        <div class="loading__content">
                            Không có kết quả.
                        </div>
                    </td>
                </tr>
            </template>
        </table>
    </div>
    <div v-else class="loading__content">
        <span class='bx bx-loader-alt loading'></span>
    </div>
</template>
<script>
export default {
    props: ['title', 'heading', 'list', 'loading', 'length', 'hideCrud'],
}
</script>
<style scoped>
    table {
        border: 1px solid #C6E2FF;
        border-collapse: collapse;
        width: 100%;
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 14px;
        text-align: left;
        border-radius: 20px;
        overflow: hidden ;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin: auto;
        margin-bottom: 50px;
    }
    table th {
        background-color: #C6E2FF;
        color: #fff;
        font-weight: 500;
        padding: 10px;
        text-transform: capitalize;
        letter-spacing: 1px;
        border-top: 1px solid #fff;
        border-bottom: 1px solid #ccc;
    }
    table tr:nth-child(even) td {
        background-color: #f2f2f2;
    }
            
    table tr:hover td {
        background-color: #E8E8E8
    }
    table td {
        background-color: #fff;
        padding: 10px;
        border-bottom: 1px solid #ccc;
        font-weight: 500;
    }
</style>
<template>
    <Base :onInput="onInput">
    <Table :loading="loading" :title="'Danh sách đăng ký nghỉ'" :list="list" :length="'8'"
        :heading="['Ảnh đại diện', 'Chuyên khoa', 'Họ tên', 'Ngày đăng ký nghỉ', 'Duyệt', 'Từ chối']" :hideCrud="true">
        <tr v-for="(item, key) in list" :key="item.id">
            <td>{{ key + 1 }}</td>
            <td><img :src="urlImage + item.avatar" style="border-radius:100rem;width: 6rem;height:6rem;object-fit: cover;"
                    alt="" srcset="">
            </td>
            <td>{{ item.namespecical }}</td>
            <td>{{ item.name }}</td>
            <td>
                <template v-if="item.dates.length > 0">
                    <p v-for="itemChild in item.dates" :key="itemChild.id">{{ itemChild.name }}</p>
                </template>
                <template v-else>
                    <p>No dates available</p>
                </template>
            </td>
            <td>
                <span @click="acceptItem(item.dates)"
                    :class="`bx bx-${loadingColumn === item.id ? 'loader loading' : 'check'}`"
                    style="font-size: 2em;"></span>
            </td>
            <td>
                <span @click="refuseItem(item.dates)"
                    :class="`bx bx-${loadingColumn === item.id ? 'loader loading' : 'x'}`" style="font-size: 2em;"></span>
            </td>
        </tr>
    </Table>
    <ModalDoctor v-if="modal.data" :reset="reset" :id="id" :setList="setList" :list="list"></ModalDoctor>
    </Base>
</template>
<script>
import Base from "./Base.vue";
import Table from "../Table.vue";
import Request from "../../../Request";
import ModalDoctor from "../modal/ModalDoctor.vue";
import { mapMutations, mapState } from "vuex";
import { URL_IMAGE } from "../../../Config";
export default {
    data() {
        return {
            list: [],
            loading: true,
            id: null,
            urlImage: URL_IMAGE,
            loadingColumn: -1,
            timeOut: null,
            counter: 0
        }
    },
    methods: {
        ...mapMutations(['setModal']),
        reset: function () {
            this.id = null;
            this.data = null;
        },
        setList: function (item) {
            const filteredList = this.list.filter(dt => dt.dates.length > 0);
            const index = filteredList.findIndex(dt => dt.idadmin === item.idadmin);
            if (index === -1) {
                this.list = [...this.list, item];
            } else {
                let clone = [...this.list];
                clone[index] = item;
                this.list = clone;
            }
        },

        acceptItem: async function (dates) {
            try {
                for (let index = 0; index < dates.length; index++) {
                    await Request.Put('/timedoctors', { ...dates[index], status: 2 });
                }
                this.fetchData();
                this.loadingColumn = -1;
            } catch (error) {
                alert(error);
            }
        },

        refuseItem: async function (dates) {
            try {
                for (let index = 0; index < dates.length; index++) {
                    await Request.Put('/timedoctors', { ...dates[index], status: 0 });
                }
                this.fetchData();
                this.loadingColumn = -1;
            } catch (error) {
                alert(error);
            }
        },

        fetchData: async function () {
            try {
                const result = await Request.Get("/doctor-admin");
                const transformedList = result.data.data.flatMap((item) => {
                    return item.dates.map((date) => ({
                        avatar: item.avatar,
                        namespecical: item.namespecical,
                        name: item.name,
                        dates: [date],
                    }));
                });
                this.list = transformedList;
                this.loading = false;
            } catch (error) {
                alert(error);
            }
        },
    },
    computed: {
        ...mapState(['modal'])
    },
    components: {
        Base,
        Table,
        ModalDoctor
    },
    mounted() {
        (async () => {
            try {
                const result = await Request.Get('/doctor-admin');
                const transformedList = result.data.data.flatMap(item => {
                    return item.dates.map(date => ({
                        avatar: item.avatar,
                        namespecical: item.namespecical,
                        name: item.name,
                        dates: [date],
                    }));
                });
                this.list = transformedList;
                this.loading = false;
            } catch (error) {
                alert(error);
            }
        })();
    }
}
</script>
<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
    color: #333;
    font-family: Arial, sans-serif;
    font-size: 14px;
    text-align: left;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    margin: auto;
    margin-top: 50px;
    margin-bottom: 50px;
}

table th {
    background-color: #ff9800;
    color: #fff;
    font-weight: bold;
    padding: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-top: 1px solid #fff;
    border-bottom: 1px solid #ccc;
}

table tr:nth-child(even) td {
    background-color: #f2f2f2;
}

table tr:hover td {
    background-color: #E8E8E8;
}

table td {
    background-color: #fff;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    font-weight: 500;
}
</style>
<template>
    <Base :onInput="onInput" :optional="[
        { id: 'all', name: 'Tất cả', color: 'black', value: null },
        { id: 1, name: 'Đã huỷ', color: 'gray', value: -1 },
        { id: 2, name: 'Chưa duyệt', color: 'red', value: 0 },
        { id: 3, name: 'Đã duyệt', color: 'green', value: 1 },
        { id: 4, name: 'Đã khám', color: 'var(--color-bold)', value: 2 },
        { id: 5, name: 'Đã phản hồi', color: 'orange', value: 3 },
    ]" :handleOptional="(item) => { this.optionalActive = item }" :optionalActive="optionalActive">
    <Table style="border-radius: 20px;" :hideCrud="true" :title="'Danh sách lịch đặt khám'" :heading="['Tên khách hàng', 'Số điện thoại', 'Lí do khám', 'Tên bác sĩ', 'Giờ khám',
        'Ngày khám', 'Thời gian đặt', 'Tình trạng', 'KQ khám', 'Sửa']" :list="list" :loading="loading">
        <tr v-for="(item, index) in (
            optionalActive.id === 'all' ? list : list.filter(dt => dt.status_book_list == optionalActive.value)
        )" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>
                {{ item.fullname_main }}
            </td>
            <td>
                {{ item.phone_main }}
            </td>
            <td>
                {{ item.reason_main }}
            </td>
            <td>
                {{ item.name_doctor }}
            </td>
            <td>
                {{ item.time }}
            </td>
            <td>
                {{ item.name + "/" + item.year }}
            </td>
            <td>
                {{ item.created_at_main }}
            </td>
            <td>
                <ItemStatus :statusMain="item.status_book_list" :isAdmin="true"></ItemStatus>
            </td>
            <td>
                <span @click="resultItem(item.fullname_main, item.status_book_list, item.reason_main, item.result, item.price, item.time)" class="bx bx-file"
                    :class="item.status_book_list === 4 || item.status_book_list === 2 || item.status_book_list === 3 ? '' : 'disabled'"></span>
            </td>
            <td>
                <span @click="editItem(item.idbooklist_main, item.status_book_list)" class="bx bx-pencil"
                    :class="item.status_book_list === -1 || item.status_book_list === 2 || item.status_book_list === 3 ? 'disabled' : ''"></span>
            </td>
        </tr>
    </Table>
    <ModalOptionData v-if="modal.data && modal.type === 'option'" :list="[{ id: -1, name: 'Huỷ lịch', status: -1, hide: status == -1 || status == 1 },
    { id: 1, name: 'Duyệt lịch', status: 1, hide: status == 1 },
    { id: 2, name: 'Đã khám', status: 2, hide: status == 0 }, { id: 3, name: 'Huỷ', status: -1, hide: status == 0 }]"
        table="booklist" :setList="setList" name="booklist-status" title="Lịch đặt khám" :id="id" :status="status">
    </ModalOptionData>
    <ModalResult v-if="modal.data && modal.type === 'result'" :list="[]" :table="modal.table" :setList="setList"
        :name="modal.name" :title="'Kết quả khám'" :id="id" :status="status" :key="modal.type" :nameProp="modal.name"
        :reasonProp="modal.reason" :resultProp="modal.result" :priceProp="modal.price" :timeProp="modal.time">
        {{ modal.data && modal.type === 'result' ? 'ModalResult Hiển thị' : 'ModalResult Ẩn' }}
    </ModalResult>

    </Base>
</template>
<script>
import Base from "./Base.vue";
// import Popup from "./abcn.vue";
import Table from "../Table.vue";
import ItemStatus from '../../user/components/ItemComponent/ItemStatus.vue';
import Request from "../../../Request";
import ModalOptionData from "../modal/ModalOptionData.vue";
import { mapMutations, mapState } from "vuex";
import ModalResult from "../modal/ModalResult.vue";
export default {
    components: {
        Base,
        Table,
        ItemStatus,
        ModalOptionData,
        ModalResult
    },
    computed: {
        ...mapState(['modal', 'admin'])
    },
    methods: {
        ...mapMutations(['setModal']),
        onInput: function (event) {
            this.value = event.target.value;
            this.loading = true;
            this.counter += 1;
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(async () => {
                try {
                    const result = await Request.Post(`/booklist-search`, {
                        value: event.target.value,
                        status: this.optionalActive.value
                    });
                    this.list = result.data.data;
                    this.loading = false;
                } catch (error) {
                    alert(error);
                }
            }, 300);
        },
        editItem: function (id, status) {
            this.id = id;
            if (status !== -1 && status !== 2 && status !== 3) {
                this.status = status;
                this.setModal({ ...this.modal, data: true, type: 'option' })
            }
        },
        resultItem: function (name, status, reason, result, price, time) {
            console.log(price)
            if (status !== -1 && status !== 4 && status !== 3) {
                this.status = status;
                this.setModal({ ...this.modal, data: true, reason: reason, name: name, type: 'result', result: result, price: price, time: time })
            }
        },
        editItem2: function () {
            alert("Bệnh nhân này chưa có lịch sử bệnh án");
        },


        setList: function (item) {
            const index = [...this.list].findIndex(dt => Number(dt.idbooklist_main) === Number(item.idbooklist_main));
            if (index !== -1) {
                let clone = [...this.list];
                clone[index] = item;
                this.list = clone;
            }
            else {
                this.list = [...this.list, item];
            }
        },
        fetchData: async function () {
            try {
                const result = await Request.Get(`/booklist-doctor/${this.admin?.id}`);
                this.list = result.data.data;
                this.loading = false;
            } catch (error) {
                alert(error);
            }
        }
    },
    data() {
        return {
            value: '',
            id: null,
            status: -2,
            list: [],
            loading: true,
            counter: 0,
            timeOut: null,
            optionalActive: { id: 'all', value: null }
        }
    },
    watch: {
        admin: function (newData) {
            if (newData) {
                this.fetchData();
            }
        },
        optionalActive: async function () {
            try {
                this.loading = true;
                const result = await Request.Post(`/booklist-search`, {
                    value: this.value,
                    status: this.optionalActive.value
                });
                this.list = result.data.data;
                this.loading = false;
            } catch (error) {
                alert(error);
            }
        }
    }
}
</script>
<style scoped>
table {
    border-collapse: collapse;
    border: 1px solid #868585;
    width: 100%;
    color: #333;
    font-size: 14px;
    text-align: left;
    border-radius: 40px;
    overflow: hidden;
    margin: auto;
    margin-top: 50px;
    margin-bottom: 50px;
}

table th {
    background-color: #C6E2FF;
    color: #fff;
    font-weight: 500;
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
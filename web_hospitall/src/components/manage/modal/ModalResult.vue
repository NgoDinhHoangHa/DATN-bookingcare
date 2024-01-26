<template>
    <ModalAdmin :title="title" nameButton="Thoát" :onSubmit="onSubmit">
        <div class="specicallist" style="display: flex; flex-direction: column; justify-content: start; align-items: start; gap: 10px;">
            <p>Tên bệnh nhân: {{ nameProp }}</p>
            <p>Giờ khám: {{ timeProp }}</p>
            <p>Triệu chứng: {{ reasonProp }}</p>
            <p>Chẩn đoán: {{ resultProp }}</p>
            <p>Phí khám: {{priceProp}}.000vnđ</p>
            <p>Đơn thuốc: </p>
            <div class="thuoc"><img src="../../../assets/img/logo/donthuoc.png" alt=""></div>
        </div>
    </ModalAdmin>
</template>
<script>
import { mapMutations, mapState } from 'vuex';
import Request from '../../../Request';
import ModalAdmin from '../ModalAdmin.vue';
export default {
    components: {
        ModalAdmin
    },
    computed: {
        ...mapState(['modal'])
    },
    props: {
        timeProp: String,
        priceProp: String,
        resultProp: String,
        nameProp: String,
        reasonProp: String,
        list: Array,
        title: String,
        handleSubmit: Function,
        setList: Function,
        name: String,
        table: String,
        id: Number,
        status: Number
    },
    methods: {
        ...mapMutations(['setModal']),
        setLoading: function (status) {
            this.setModal({ ...this.modal, loading: status })
        },
        onChange: function (event) {
            this.statusUpdate = event.target.value
        },
        onSubmit: async function () {
            try {
                const result = await Request.Post(`/${this.table}-status`, {
                    id: this.id,
                    status: this.statusUpdate
                });
                this.setList(result.data.data);
                this.closeModal();
            } catch (error) {
                alert(error);
            }
        },
        closeModal: function () {
            this.setModal({ ...this.modal, loading: true, data: null })
        },
    },
    data() {
        return {
            statusUpdate: `${this.status}`
        }
    },
    mounted() {
        this.setLoading(true);
        this.setLoading(false);
    }
}
</script>
<style scoped>
    .thuoc{
        width: 430px;
        height: 350px;
        padding: 10px;
    }
    img {
        width: 100%;
        height: 100%;
        image-rendering: pixelated;
        
    }
</style>
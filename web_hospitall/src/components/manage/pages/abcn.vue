<template>
    <div class="modal">
        <div class="modal__title">Thông báo</div>
        <div class="modal__body">lịch suử bệnh nhân chưa tồn tại</div>
        <div class="modal__footer">
            <button>Cancel</button>
            <button>OK</button>
        </div>
      </div>
      
</template>
const mutations = {
    showModal(state, payload) {
      state.modalVisible = true
      state.dialogContent = payload
    },
    hideModal(state) {
      state.modalVisible = false
    },
  }
  
  const state = {
    modalVisible: false,
    dialogContent: {},
  }
  
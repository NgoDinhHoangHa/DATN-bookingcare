<template>
    <header id="header" class="header" style="height: 80px; text-align: center;  ">
        <div class="wrapper header__content" style="justify-content: center; margin-top: 60px;">
            <div class="header__logo">
                <router-link :to="{ name: 'User_Home' }">
                    <img src="https://bookingcare.vn/assets/icon/bookingcare-2020.svg" alt="" srcset="" style="margin-left: 40px;">
                </router-link>
            </div>
            <div id="menu">
                <ul>
                    <li>
                        <router-link @click="isActive = !isActive" :to="{
                            name: 'User_Home'
                        }">
                            Trang chủ
                        </router-link>
                    </li>
                    <li v-if="user">
                        <router-link @click="isActive = !isActive" :to="{
                            name: 'Profile',
                            params: {
                                id: this.user.id
                            }
                        }">
                            Cập nhật thông tin cá nhân
                        </router-link>
                    </li>
                    <li>
                        <router-link @click="isActive = !isActive" :to="{
                            name: 'DoctorList'
                        }">
                            Bác sĩ
                        </router-link>
                    </li>
                    <li>
                        <router-link @click="isActive = !isActive" :to="{
                            name: 'Blogs'
                        }">
                            Bài viết
                        </router-link>
                    </li>
                </ul>
                </div>
            <div class="header__support" style="margin-left: 60px;" @click="this.$router.push({ name: (user ? 'Profile' : 'Login') })">
                <i class='bx bxs-user' style='color:#318c9c'></i>
                <span>{{ user ? user.fullname : "Đăng nhập" }}</span>
            </div>
            <div style="margin-left: 20px;" @click="logout()" v-if="user != null" class="header__support">
                <i class='bx bx-exit' style='color:#318c9c'></i>
                <span></span>
            </div>
        </div>
    </header>
</template>
<style scoped>
    * {
        margin: 0;
        padding: 0;
    }
    #menu ul {
        list-style-type: none;
        text-align: center;
        width: 100%;
    }
    #menu li {
        color: #f1f1f1;
        display: inline-block;
        margin-left: 50px;
      }
    
</style>
<script>
import { mapState, mapMutations } from 'vuex'
export default {
    data() {
        return {
            isActive: false
        }
    },
    computed: {
        ...mapState(['user'])
    },
    methods: {
        ...mapMutations(['setuser']),
        logout: function () {
            let auth = confirm('Bạn có muốn đăng xuất?')
            if (auth) {
                window.localStorage.removeItem('K-user')
                this.setuser(null)
                this.$router.push({ name: 'Login' });
            }
        }
    }
}
</script>
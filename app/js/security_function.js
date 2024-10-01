// Nhập Firebase App
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-app.js";
import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-messaging.js";
import { getFirestore, collection, addDoc, getDocs, deleteDoc, doc } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-firestore.js";
import { getDatabase, ref, set, onValue } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-database.js";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCc80A_SdAfkoVioULDDinlSUFJtjAlDAA",
    authDomain: "bookingflight-76b84.firebaseapp.com",
    databaseURL: "https://bookingflight-76b84-default-rtdb.firebaseio.com",
    projectId: "bookingflight-76b84",
    storageBucket: "bookingflight-76b84.appspot.com",
    messagingSenderId: "360606142971",
    appId: "1:360606142971:web:08e335025bfc66d7b2fb5e",
    measurementId: "G-NFJM33SXQR"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const database = getFirestore(app);
const db = getDatabase(app);
const messaging = getMessaging(app);

// Request permission for notifications
Notification.requestPermission().then((permission) => {
    if (permission === "granted") {
        console.log("Notification permission granted.");
        getToken(messaging, { vapidKey: 'BC5Q4yN9d3X1wXykK_5t9wzF3_yFj32sKf4s3gM8safBqJXU7qE9_tGr0DZ6bB1tZ85O9D5bZFMExDbA6qLOtG-I' })
            .then((token) => {
                if (token) {
                    console.log('Token received: ', token);
                } else {
                    console.error('No registration token available. Request permission to generate one.');
                }
            }).catch((err) => {
                console.error('An error occurred while retrieving token. ', err);
            });
    } else {
        console.log("Unable to get permission to notify.");
    }
});

// Gửi thông báo lên Firebase khi form được gửi
document.getElementById('form').addEventListener('submit', function (event) {
    event.preventDefault(); // Ngăn chặn hành động mặc định của form

    var datos = new FormData(event.target); 
    var jsonObject = {};
    datos.forEach(function (value, key) {
        jsonObject[key] = value;
    });

    let thongBao = datos.get('thongBao');
    let ngayTao = datos.get('ngayTao');

    let message = document.querySelector("#message");
    message.innerHTML = "";
    if (thongBao && ngayTao) {
        const newNotificationRef = ref(db, 'notifications/' + Date.now());
        set(newNotificationRef, {
            thongBao: thongBao,
            ngayTao: ngayTao,
        }).then(() => {
            console.log('Thông báo đã được gửi lên Firebase!');
            document.getElementById('form').reset();
            print_table();
        }).catch((error) => {
            console.error('Lỗi khi gửi thông báo:', error);
            showError('Có lỗi xảy ra khi gửi thông báo.');
        });
    } else {
        // Xử lý lỗi nếu có trường không hợp lệ
        if (!thongBao) {
            document.getElementById('thongBaoError').innerText = 'Vui lòng nhập thông báo!';
        } else {
            document.getElementById('thongBaoError').innerText = '';
        }

        if (!ngayTao) {
            document.getElementById('ngayTaoError').innerText = 'Vui lòng chọn ngày giờ!';
        } else {
            document.getElementById('ngayTaoError').innerText = '';
        }
    }
});

function print_table() {
    const notificationsRef = ref(db, 'notifications/');
    
    onValue(notificationsRef, (snapshot) => {
        const data = snapshot.val();
        const tbody = document.querySelector('#myDataTable .tbody');
        tbody.innerHTML = ''; // Xóa dữ liệu cũ

        for (const key in data) {
            const notification = data[key];
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${notification.thongBao}</td>
                <td>${new Date(notification.ngayTao).toLocaleString()}</td>
                <td><button class="btn btn-danger" onclick="deleteNotification('${key}')">Xóa</button></td>
            `;
            tbody.appendChild(row);
        }
    });
}

// Hàm xóa thông báo
function deleteNotification(key) {
    const notificationRef = ref(db, 'notifications/' + key);
    
    Swal.fire({
        title: 'Bạn đã chắc chắn chưa?',
        text: "Bạn sẽ không còn dữ liệu sau khi xóa",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tôi đồng ý'
    }).then((result) => {
        if (result.isConfirmed) {
            set(notificationRef, null).then(() => {
                print_table(); // Cập nhật bảng sau khi xóa
                Swal.fire('Đã xóa', 'Bạn đã xóa thành công.', 'success');
            }).catch((error) => {
                console.error('Lỗi khi xóa thông báo:', error);
                Swal.fire('Lỗi', 'Có lỗi xảy ra khi xóa thông báo.', 'error');
            });
        }
    });
}

// Gọi hàm để hiển thị dữ liệu ngay khi trang được tải
document.addEventListener('DOMContentLoaded', print_table);

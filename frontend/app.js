// Konfigurasi API Server Backend
const BASE_URL = 'http://127.0.0.1:8002/api'; // ← Samakan ke satu server!

// ==========================================
// PENGATURAN TAMPILAN (UI STATE)
// ==========================================
function cekStatusLayar() {
    if (localStorage.getItem('jwt_token')) {
        document.getElementById('loginBox').style.display = 'none';
        document.getElementById('dashboardBox').style.display = 'block';
    } else {
        document.getElementById('loginBox').style.display = 'block';
        document.getElementById('dashboardBox').style.display = 'none';
    }
}

cekStatusLayar();

// ==========================================
// LOGIKA TOMBOL UI KE API
// ==========================================
async function prosesLogin() {
    const email = document.getElementById('emailInput').value;
    const pass = document.getElementById('passwordInput').value;

    if (!email || !pass) return alert("Email dan password wajib diisi!");

    try {
        const response = await fetch(`${BASE_URL}/login`, { // ← Sekarang ke 8002
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password: pass })
        });

        const result = await response.json();
        console.log('Login response:', result); // Untuk debug

        if (response.ok && result.access_token) {
            localStorage.setItem('jwt_token', result.access_token);
            alert("Login Sukses!");
            cekStatusLayar();
        } else {
            alert("Login Gagal: " + (result.message || "Cek email dan password Anda."));
        }
    } catch (error) {
        alert("Gagal terhubung ke server API.");
        console.error(error);
    }
}

async function submitCheckIn() {
    const token = localStorage.getItem('jwt_token');
    if (!token) return alert("Sesi habis, silakan login kembali.");

    try {
        const response = await fetch(`${BASE_URL}/attendence/check-in`, { // ← Pakai BASE_URL
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const result = await response.json();
        console.log('CheckIn response:', result); // Untuk debug

        if (response.status === 201 || response.ok) {
            alert("Sukses: " + result.message);
        } else if (response.status === 401) {
            alert("Sesi expired, silakan login ulang.");
            prosesLogout();
        } else {
            alert("Gagal: " + (result.message || "Terjadi kesalahan."));
        }
    } catch (error) {
        console.error(error);
        alert("Gagal terhubung ke server.");
    }
}

async function submitCheckOut() {
    const token = localStorage.getItem('jwt_token');
    if (!token) return alert("Sesi habis, silakan login kembali.");

    try {
        const response = await fetch(`${BASE_URL}/attendence/check-out`, { // ← Pakai BASE_URL
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const result = await response.json();
        console.log('CheckOut response:', result); // Untuk debug

        if (response.ok) {
            alert("Sukses: " + result.message);
        } else if (response.status === 401) {
            alert("Sesi expired, silakan login ulang.");
            prosesLogout();
        } else {
            alert("Gagal: " + (result.message || "Terjadi kesalahan."));
        }
    } catch (error) {
        console.error(error);
        alert("Gagal terhubung ke server.");
    }
}

function prosesLogout() {
    localStorage.removeItem('jwt_token');
    cekStatusLayar();
    document.getElementById('emailInput').value = '';
    document.getElementById('passwordInput').value = '';
}
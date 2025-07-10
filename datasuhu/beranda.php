<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <?php
        include "../navbar.php";
    ?>
    <div class="container mx-auto p-4 mt-4 bg-white rounded shadow-lg">
        <h2 class="text-2xl font-bold mb-2">Informasi Sistem</h2>
        <p class="text-gray-700 mb-4">
            Partner coding
            Tampilkan alur berpikir
            Kode program ini dirancang untuk mikrokontroler ESP8266 yang berfungsi sebagai perangkat monitoring suhu. Setelah menyala, perangkat akan secara otomatis mencoba terhubung ke jaringan Wi-Fi dengan nama "Maintenance" dan kata sandi "0987654321". Setelah koneksi Wi-Fi berhasil, ia akan menampilkan alamat IP lokalnya di Serial Monitor.<br><br>
            Pada setiap siklus, perangkat akan membaca suhu dari sensor DS18B20 yang terhubung ke pin D2. Hasil pembacaan suhu, baik dalam Celsius maupun Fahrenheit, akan ditampilkan di Serial Monitor. Selain itu, suhu dalam format Celsius akan dikirimkan melalui protokol UDP ke alamat IP "192.168.67.50" pada port 11000, diawali dengan pengidentifikasi "ID01;". Proses pembacaan dan pengiriman data ini akan berulang setiap satu menit, sehingga mengurangi beban kerja pada komponen.
        </p>
    </div>
</body>
</html>
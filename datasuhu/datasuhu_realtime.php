<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemantauan Suhu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php
        include "../navbar.php";
        include "../koneksi/koneksi.php";

        // Ambil daftar lokasi unik dari database untuk filter
        $lokasi_options = [];
        $sql_lokasi = "SELECT DISTINCT id_perangkat FROM tbl_temperatur ORDER BY id_perangkat ASC";
        $result_lokasi = mysqli_query($conn, $sql_lokasi);
        while($row = mysqli_fetch_assoc($result_lokasi)) {
            $lokasi_options[] = $row['id_perangkat'];
        }

        // Ambil nilai filter dari URL
        $selected_lokasi = $_GET['lokasi'] ?? 'semua';
        $start_time_input = $_GET['start_time'] ?? '';

        // Bangun klausa WHERE untuk query SQL
        $where_clauses = [];
        $page_mode = "Mode Realtime (30 Menit Terakhir)";
        $chart_title = "Grafik Tren Suhu (30 Menit Terakhir)";

        // Filter untuk Lokasi
        if ($selected_lokasi != 'semua' && in_array($selected_lokasi, $lokasi_options)) {
            $where_clauses[] = "id_perangkat = '" . mysqli_real_escape_string($conn, $selected_lokasi) . "'";
        }

        // Filter untuk Waktu
        if (!empty($start_time_input)) {
            // Jika ada input waktu, kita masuk ke "Mode Demo"
            try {
                $start_time_dt = new DateTime($start_time_input);
                $end_time_dt = (clone $start_time_dt)->add(new DateInterval('PT30M')); // Jendela waktu 30 menit
                
                $start_time_sql = $start_time_dt->format('Y-m-d H:i:s');
                $end_time_sql = $end_time_dt->format('Y-m-d H:i:s');
                
                $where_clauses[] = "tanggal BETWEEN '$start_time_sql' AND '$end_time_sql'";
                
                $page_mode = "Mode Demo";
                $chart_title = "Grafik Suhu pada " . $start_time_dt->format('d M Y, H:i');

            } catch (Exception $e) {
                // Jika format tanggal salah, kembali ke mode realtime
                $where_clauses[] = "tanggal >= NOW() - INTERVAL 30 MINUTE";
            }
        } else {
            // Default ke "Mode Realtime"
            $where_clauses[] = "tanggal >= NOW() - INTERVAL 30 MINUTE";
        }

        // Gabungkan semua klausa WHERE
        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = "WHERE " . implode(' AND ', $where_clauses);
        }

        // Eksekusi semua query dengan filter yang sudah dibuat
        $sql_latest = "SELECT nilai_temperatur FROM tbl_temperatur $where_sql ORDER BY tanggal DESC LIMIT 1";
        $result_latest = mysqli_query($conn, $sql_latest);
        $latest_data = mysqli_fetch_assoc($result_latest);
        $suhu_terakhir = $latest_data['nilai_temperatur'] ?? null;
        
        // Analisis Fuzzy (kode tidak berubah)
        $label_kenyamanan = "Tidak Diketahui";
        $skor_kenyamanan = 0;
        if ($suhu_terakhir !== null) {
            $python_venv_path = "D:\\Kuliah\\IOT\\tugas_akhir_semester\\Monitoring-ESP\\.venv\\Scripts\\python.exe";
            $python_script_path = "D:\\Kuliah\\IOT\\tugas_akhir_semester\\Monitoring-ESP\\scripts\\analisis_kenyamanan.py";
            $command = escapeshellarg($python_venv_path) . " " . escapeshellarg($python_script_path) . " " . escapeshellarg($suhu_terakhir);
            $output = shell_exec($command);
            if ($output && strpos($output, '#') !== false) {
                list($label_kenyamanan, $skor_kenyamanan) = explode('#', trim($output));
            }
        }

        $sql_chart = "SELECT AVG(nilai_temperatur) as rata_suhu, DATE_FORMAT(MIN(tanggal), '%H:%i') as waktu FROM tbl_temperatur $where_sql GROUP BY UNIX_TIMESTAMP(tanggal) DIV 180 ORDER BY MIN(tanggal) ASC";
        $result_chart = mysqli_query($conn, $sql_chart);
        $chart_labels = [];
        $chart_data = [];
        while($row = mysqli_fetch_assoc($result_chart)) {
            $chart_labels[] = $row['waktu'];
            $chart_data[] = round($row['rata_suhu'], 2);
        }
        
        $sql_stats = "SELECT MAX(nilai_temperatur) as max_suhu, MIN(nilai_temperatur) as min_suhu, AVG(nilai_temperatur) as avg_suhu FROM tbl_temperatur $where_sql";
        $result_stats = mysqli_query($conn, $sql_stats);
        $stats = mysqli_fetch_assoc($result_stats);
    ?>

    <div class="container mx-auto p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-white p-4 rounded-lg shadow-md">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Pemantauan Suhu</h1>
                <p class="text-lg text-gray-600">
                    Menampilkan data untuk lokasi: <strong class="text-blue-600"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $selected_lokasi))); ?></strong>
                </p>
            </div>

            <form action="datasuhu_realtime.php" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-4 mt-4 md:mt-0">
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <select name="lokasi" id="lokasi" class="mt-1 block w-full md:w-auto bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua" <?php echo ($selected_lokasi == 'semua') ? 'selected' : ''; ?>>Semua Lokasi</option>
                        <?php foreach ($lokasi_options as $lokasi) : ?>
                            <option value="<?php echo htmlspecialchars($lokasi); ?>" <?php echo ($selected_lokasi == $lokasi) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lokasi); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Demo (opsional)</label>
                    <input type="datetime-local" id="start_time" name="start_time" value="<?php echo htmlspecialchars($start_time_input); ?>" class="mt-1 block w-full md:w-auto bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="self-end bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Terapkan
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md col-span-1 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Analisis Kenyamanan Kerja (AI)</h2>
                <div class="flex items-center justify-center text-center">
                    <div class="w-1/2">
                        <p class="text-lg text-gray-600">Suhu Terakhir</p>
                        <p class="text-5xl font-bold text-blue-600 my-2">
                            <?php echo $suhu_terakhir !== null ? number_format($suhu_terakhir, 1) . "°C" : "N/A"; ?>
                        </p>
                    </div>
                    <div class="border-l-2 border-gray-200 h-24 mx-4"></div>
                    <div class="w-1/2">
                        <p class="text-lg text-gray-600">Hasil Analisis Fuzzy</p>
                         <div class="mt-2 text-2xl font-bold p-2 rounded-lg 
                            <?php 
                                if ($label_kenyamanan == 'Sangat Nyaman') echo 'bg-green-100 text-green-800';
                                elseif ($label_kenyamanan == 'Cukup Nyaman') echo 'bg-yellow-100 text-yellow-800';
                                else echo 'bg-red-100 text-red-800';
                            ?>">
                            <?php echo $label_kenyamanan; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Skor: <?php echo number_format($skor_kenyamanan, 1); ?>/100</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Statistik - <span class="text-blue-600"><?php echo $page_mode; ?></span></h2>
                <div class="space-y-3">
                    <p class="flex justify-between text-lg">Tertinggi: <span class="font-bold text-red-500"><?php echo number_format($stats['max_suhu'] ?? 0, 1); ?>°C</span></p>
                    <p class="flex justify-between text-lg">Terendah: <span class="font-bold text-blue-500"><?php echo number_format($stats['min_suhu'] ?? 0, 1); ?>°C</span></p>
                    <p class="flex justify-between text-lg">Rata-rata: <span class="font-bold text-gray-700"><?php echo number_format($stats['avg_suhu'] ?? 0, 1); ?>°C</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4"><?php echo $chart_title; ?></h2>
            <div class="relative h-96">
                <canvas id="GrafikSuhu"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('GrafikSuhu').getContext('2d');
        const labels = <?php echo json_encode($chart_labels); ?>;
        const dataSuhu = <?php echo json_encode($chart_data); ?>;

        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Suhu (°C)",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    data: dataSuhu,
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false, title: { display: true, text: 'Suhu (°C)' } },
                    x: { title: { display: true, text: 'Waktu' } }
                }
            }
        });
    </script>
</body>
</html>
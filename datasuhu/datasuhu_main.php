<!DOCTYPE html>
<html lang="en">
<head>
    <title>Informasi Data Suhu</title>
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" name="viewport"/>
    <meta content="Mandi" name="author"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php
        include "../navbar.php";
    ?>
    <div class="container mx-auto p-4 mt-4 bg-white rounded shadow-lg">
        <h2 class="text-2xl font-bold mb-2">Informasi Data Suhu</h2>
        <p class="text-gray-700 mb-4">Sistem Monitoring Suhu - Universitas Medan Area</p>

        <table id="mytable" class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">No</th>
                    <th class="py-3 px-6 text-left">Id</th>
                    <th class="py-3 px-6 text-left">Id Perangkat</th>
                    <th class="py-3 px-6 text-left">Nilai Temperatur</th>
                    <th class="py-3 px-6 text-left">Waktu</th>
                    <th class="py-3 px-6 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php
                    // Menampilkan data mysqli
                    include "../koneksi/koneksi.php";
                    $no = 0;
                    // Perbaikan: FORM menjadi FROM
                    $result=mysqli_query($conn,"SELECT * FROM tbl_temperatur");
                    while($r=mysqli_fetch_array($result)) {
                    $no++;
                ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo $no; ?></td>
                    <td class="py-3 px-6 text-left"><?php echo $r['id']; ?></td> <td class="py-3 px-6 text-left"><?php echo $r['id_perangkat']; ?></td>
                    <td class="py-3 px-6 text-left"><?php echo $r['nilai_temperatur']; ?></td>
                    <td class="py-3 px-6 text-left"><?php echo $r['tanggal']; ?></td>
                    <td class="py-3 px-6 text-left">
                        <a href="#" onclick="konfirmasi_hapus('proses_delete.php?&id=<?php echo $r['id']; ?>&namatable=tbl_temperatur');" class="text-red-500 hover:text-red-700">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="modal_delete">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Anda yakin akan menghapus data ini?</h3>
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="#" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" id="delete_link">Hapus</a>
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2" onclick="document.getElementById('modal_delete').classList.add('hidden')">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function konfirmasi_hapus(delete_url) {
            document.getElementById('modal_delete').classList.remove('hidden');
            document.getElementById('delete_link').setAttribute('href' ,delete_url);
        }
     </script>
</body>
</html>
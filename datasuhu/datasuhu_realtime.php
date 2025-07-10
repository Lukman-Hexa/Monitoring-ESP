<!DOCTYPE html>
<html lang="en">
    <!-- <script src="Chart.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- <body class="bg-gray-100 p-4"> -->
<body class="bg-gray-100">
    <?php
        include "../navbar.php";
    ?>
    <div class="container mx-auto bg-white p-6 rounded shadow-lg">
        <canvas id="GrafikBatang" class="w-full max-w-4xl mx-auto"></canvas>
    </div>
    <?php
        // menampilkan data myslqi
        include "../koneksi/koneksi.php";
        $no = 0;
        $arrdata=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        // 24 Jam
        // $sql ="select DATE_FORMAT(tanggal, '%H') as jam, avg(nilai_temperatur) as ratarata from tbl_temperatur group by DATE_FORMAT(tanggal, '%Y-%m-%d %H')";
        $sql = "SELECT DATE_FORMAT(tanggal, '%H') as jam, AVG(nilai_temperatur) as ratarata 
        FROM tbl_temperatur 
        GROUP BY DATE_FORMAT(tanggal, '%H')";
        $result=mysqli_query($conn, $sql);
        while($r=mysqli_fetch_array($result)) {
            $posisiarray=$r['jam']*1; //dikali 1 supaya jadi integer, jadi 01 jadi 1
            $arrdata[$posisiarray]=$r['ratarata'];
            // echo $r['jam']."<br>"; // Mengomentari baris debug ini
        }
    ?>
    <script>
        var xValues =["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];
        var yValues =[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        <?php
            for ($i=0; $i<24; $i++) {
                echo "yValues[$i]=$arrdata[$i];";
            }
        ?>
        var barColors = ["red","green","blue","orange","brown","yellow","red","green","blue","orange","brown","yellow", "purple", "cyan", "magenta", "pink", "lime", "teal", "indigo", "violet", "gold", "silver", "maroon", "navy"];

        new Chart("GrafikBatang", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                legend: {display: false}, // Perbaikan: 'flase' menjadi 'false'
                title: {
                    display: true,
                    text: "Data Suhu - Lokasi"
                },
                onClick: function (e) { // Perbaikan: 'onclick' menjadi 'onClick' sesuai dengan Chart.js
                    // debugger; // Umumnya tidak diperlukan di produksi
                    var activePoint = this.getElementsAtEvent(e)[0];
                    if (activePoint) { // Periksa apakah ada titik yang diklik
                        var activePointLabel = activePoint._model.label;
                        fn_test(activePointLabel);
                    }
                }
            }
        });
    </script>
    <script>
        function fn_test(param) {
            alert("Testing:"+param);
        }
    </script>
</body>
</html>
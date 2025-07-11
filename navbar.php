<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>Sistem Monitoring Suhu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav class="bg-blue-600 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a class="text-white text-xl font-bold" href="#">Sistem Monitoring Suhu</a>
            <button class="text-white md:hidden focus:outline-none" aria-expanded="false" aria-controls="navbar" onclick="document.getElementById('navbar-collapse').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
            <div id="navbar-collapse" class="hidden md:flex md:items-center md:w-auto w-full">
                <ul class="md:flex items-center justify-between text-base text-white pt-4 md:pt-0">
                    <li><a class="block md:inline-block py-2 px-3 hover:bg-blue-700 rounded" href="../datasuhu/beranda.php">Home</a></li>
                    <li><a class="block md:inline-block py-2 px-3 hover:bg-blue-700 rounded" href="../datasuhu/datasuhu_main.php">Data Suhu</a></li>
                    <li><a class="block md:inline-block py-2 px-3 hover:bg-blue-700 rounded" href="../datasuhu/datasuhu_realtime.php">Grafik Suhu</a></li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
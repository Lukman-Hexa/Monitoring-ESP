import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import sys
import matplotlib.pyplot as plt

# =====================================================================
# BAGIAN 1: DEFINISI LOGIKA FUZZY
# (Tidak ada perubahan signifikan, hanya diorganisir)
# =====================================================================

def create_fuzzy_system():
    """Menciptakan dan mengkonfigurasi sistem kontrol fuzzy."""
    # 1. Mendefinisikan Variabel Semesta (Input dan Output)
    suhu = ctrl.Antecedent(np.arange(15, 36, 1), 'suhu')
    kenyamanan = ctrl.Consequent(np.arange(0, 101, 1), 'kenyamanan')

    # 2. Mendefinisikan Fungsi Keanggotaan (Membership Functions)
    suhu['sangat_dingin'] = fuzz.trimf(suhu.universe, [15, 15, 19])
    suhu['dingin'] = fuzz.trimf(suhu.universe, [17, 20, 23])
    suhu['nyaman'] = fuzz.trimf(suhu.universe, [22, 24.5, 27])
    suhu['hangat'] = fuzz.trimf(suhu.universe, [26, 28, 30])
    suhu['panas'] = fuzz.trimf(suhu.universe, [29, 35, 35])

    kenyamanan['tidak_nyaman'] = fuzz.trimf(kenyamanan.universe, [0, 0, 50])
    kenyamanan['cukup_nyaman'] = fuzz.trimf(kenyamanan.universe, [40, 65, 90])
    kenyamanan['sangat_nyaman'] = fuzz.trimf(kenyamanan.universe, [80, 100, 100])

    # 3. Membuat Aturan Fuzzy (Rules)
    rule1 = ctrl.Rule(suhu['sangat_dingin'] | suhu['panas'], kenyamanan['tidak_nyaman'])
    rule2 = ctrl.Rule(suhu['dingin'] | suhu['hangat'], kenyamanan['cukup_nyaman'])
    rule3 = ctrl.Rule(suhu['nyaman'], kenyamanan['sangat_nyaman'])

    # 4. Membuat Sistem Kontrol
    sistem_kontrol = ctrl.ControlSystem([rule1, rule2, rule3])
    return ctrl.ControlSystemSimulation(sistem_kontrol)

def analyze_comfort(simulasi, temperature_input):
    """Melakukan analisis kenyamanan untuk suhu yang diberikan."""
    simulasi.input['suhu'] = temperature_input
    simulasi.compute()
    
    hasil_skor = simulasi.output['kenyamanan']
    if hasil_skor <= 55:
        label = "Tidak Nyaman"
    elif 55 < hasil_skor <= 85:
        label = "Cukup Nyaman"
    else:
        label = "Sangat Nyaman"
        
    return f"{label}#{round(hasil_skor, 2)}"

# =====================================================================
# BAGIAN 2: FUNGSI UNTUK VISUALISASI
# (Kode plot Anda dipindahkan ke dalam fungsi ini)
# =====================================================================

def visualize_membership_functions():
    """Membuat dan menampilkan plot fungsi keanggotaan."""
    suhu = ctrl.Antecedent(np.arange(15, 36, 1), 'suhu')
    kenyamanan = ctrl.Consequent(np.arange(0, 101, 1), 'kenyamanan')

    # Fungsi keanggotaan suhu
    suhu['sangat_dingin'] = fuzz.trimf(suhu.universe, [15, 15, 19])
    suhu['dingin'] = fuzz.trimf(suhu.universe, [17, 20, 23])
    suhu['nyaman'] = fuzz.trimf(suhu.universe, [22, 24.5, 27])
    suhu['hangat'] = fuzz.trimf(suhu.universe, [26, 28, 30])
    suhu['panas'] = fuzz.trimf(suhu.universe, [29, 35, 35])

    # Fungsi keanggotaan kenyamanan
    kenyamanan['tidak_nyaman'] = fuzz.trimf(kenyamanan.universe, [0, 0, 50])
    kenyamanan['cukup_nyaman'] = fuzz.trimf(kenyamanan.universe, [40, 65, 90])
    kenyamanan['sangat_nyaman'] = fuzz.trimf(kenyamanan.universe, [80, 100, 100])
    
    # Membuat visualisasi
    fig, (ax0, ax1) = plt.subplots(nrows=2, figsize=(10, 8))

    ax0.plot(suhu.universe, suhu['sangat_dingin'].mf, 'b', linewidth=1.5, label='Sangat Dingin')
    ax0.plot(suhu.universe, suhu['dingin'].mf, 'g', linewidth=1.5, label='Dingin')
    ax0.plot(suhu.universe, suhu['nyaman'].mf, 'r', linewidth=1.5, label='Nyaman')
    ax0.plot(suhu.universe, suhu['hangat'].mf, 'c', linewidth=1.5, label='Hangat')
    ax0.plot(suhu.universe, suhu['panas'].mf, 'm', linewidth=1.5, label='Panas')
    ax0.set_title('Fungsi Keanggotaan Suhu')
    ax0.set_xlabel('Suhu (°C)')
    ax0.set_ylabel('Derajat Keanggotaan')
    ax0.legend()
    ax0.grid(True)

    ax1.plot(kenyamanan.universe, kenyamanan['tidak_nyaman'].mf, 'b', linewidth=1.5, label='Tidak Nyaman')
    ax1.plot(kenyamanan.universe, kenyamanan['cukup_nyaman'].mf, 'g', linewidth=1.5, label='Cukup Nyaman')
    ax1.plot(kenyamanan.universe, kenyamanan['sangat_nyaman'].mf, 'r', linewidth=1.5, label='Sangat Nyaman')
    ax1.set_title('Fungsi Keanggotaan Kenyamanan')
    ax1.set_xlabel('Skor Kenyamanan')
    ax1.set_ylabel('Derajat Keanggotaan')
    ax1.legend()
    ax1.grid(True)
    
    plt.tight_layout()
    plt.show()

# =====================================================================
# BAGIAN 3: EKSEKUSI UTAMA
# (Memproses argumen dari command line)
# =====================================================================

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # Periksa apakah argumen adalah untuk plot
        if sys.argv[1] == '--plot':
            print("Menampilkan visualisasi fungsi keanggotaan...")
            visualize_membership_functions()
        # Jika bukan, proses sebagai input suhu
        else:
            try:
                input_temp = float(sys.argv[1])
                # Buat sistem fuzzy
                simulasi_kenyamanan = create_fuzzy_system()
                # Dapatkan hasil analisis
                hasil = analyze_comfort(simulasi_kenyamanan, input_temp)
                # Cetak hasil untuk ditangkap PHP
                print(hasil)
            except ValueError:
                print("Error: Input suhu tidak valid. Gunakan angka atau '--plot'.")
    else:
        print("Error: Tidak ada argumen yang diberikan.")
        print("Penggunaan:")
        print("  - Untuk analisis: python analisis_kenyamanan.py <suhu>")
        print("  - Untuk visualisasi: python analisis_kenyamanan.py --plot")
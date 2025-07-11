# ----------------------------------------------------
# FILE: app.py (VERSI FINAL YANG SUDAH DIPERBAIKI)
# ----------------------------------------------------
from flask import Flask, request, jsonify
import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl

# Inisialisasi aplikasi Flask
app = Flask(__name__)

# --- Bagian Logika Fuzzy ---
# Variabel global untuk sistem fuzzy agar tidak dibuat berulang-ulang
# Ini membuat performa lebih cepat
suhu_antecedent = ctrl.Antecedent(np.arange(15, 36, 1), 'suhu')
kenyamanan_consequent = ctrl.Consequent(np.arange(0, 101, 1), 'kenyamanan')

# Mendefinisikan Fungsi Keanggotaan (Membership Functions)
suhu_antecedent['sangat_dingin'] = fuzz.trimf(suhu_antecedent.universe, [15, 15, 19])
suhu_antecedent['dingin'] = fuzz.trimf(suhu_antecedent.universe, [17, 20, 23])
suhu_antecedent['nyaman'] = fuzz.trimf(suhu_antecedent.universe, [22, 24.5, 27])
suhu_antecedent['hangat'] = fuzz.trimf(suhu_antecedent.universe, [26, 28, 30])
suhu_antecedent['panas'] = fuzz.trimf(suhu_antecedent.universe, [29, 35, 35])

kenyamanan_consequent['tidak_nyaman'] = fuzz.trimf(kenyamanan_consequent.universe, [0, 0, 50])
kenyamanan_consequent['cukup_nyaman'] = fuzz.trimf(kenyamanan_consequent.universe, [40, 65, 90])
kenyamanan_consequent['sangat_nyaman'] = fuzz.trimf(kenyamanan_consequent.universe, [80, 100, 100])

# Membuat Aturan Fuzzy (Rules)
rule1 = ctrl.Rule(suhu_antecedent['sangat_dingin'] | suhu_antecedent['panas'], kenyamanan_consequent['tidak_nyaman'])
rule2 = ctrl.Rule(suhu_antecedent['dingin'] | suhu_antecedent['hangat'], kenyamanan_consequent['cukup_nyaman'])
rule3 = ctrl.Rule(suhu_antecedent['nyaman'], kenyamanan_consequent['sangat_nyaman'])

# Membuat Sistem Kontrol
sistem_kontrol = ctrl.ControlSystem([rule1, rule2, rule3])
simulasi = ctrl.ControlSystemSimulation(sistem_kontrol)

# --- Bagian API Endpoint ---
@app.route('/analisis', methods=['GET'])
def api_analisis():
    # Ambil parameter suhu dari URL
    suhu_value = request.args.get('suhu')

    if suhu_value is None:
        return jsonify({"status": "error", "message": "Parameter 'suhu' tidak ditemukan."}), 400

    try:
        # Lakukan analisis kenyamanan untuk suhu yang diberikan
        simulasi.input['suhu'] = float(suhu_value)
        simulasi.compute()

        hasil_skor = simulasi.output['kenyamanan']

        # Tentukan label berdasarkan skor
        if hasil_skor <= 55:
            label = "Tidak Nyaman"
        elif 55 < hasil_skor <= 85:
            label = "Cukup Nyaman"
        else:
            label = "Sangat Nyaman"

        # Siapkan respons yang akan dikirim kembali sebagai JSON
        response = {
            "status": "success",
            "label": label,
            "skor": round(hasil_skor, 2)
        }
        return jsonify(response)

    except Exception as e:
        # Jika ada error saat komputasi fuzzy, kirim pesan error
        return jsonify({"status": "error", "message": str(e)}), 500

# --- Bagian untuk Menjalankan Server ---
if __name__ == '__main__':
    # Berjalan di localhost port 5000 dengan debug mode aktif
    app.run(host='127.0.0.1', port=5000, debug=True)
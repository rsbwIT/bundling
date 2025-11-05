<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Display Kamar RS</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #1f2937, #111827);
        color: #f9fafb;
        margin: 0;
        padding: 20px;
    }
    h1 {
        text-align: center;
        font-size: 4rem;
        margin-bottom: 50px;
        background: linear-gradient(to right, #3b82f6, #06b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .ruangan-header {
        background: linear-gradient(to right, #1e3a8a, #3b82f6);
        padding: 25px;
        border-radius: 15px 15px 0 0;
        margin-top: 40px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        animation: pulse 4s infinite alternate;
    }
    @keyframes pulse {
        0% { box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
        50% { box-shadow: 0 12px 30px rgba(0,0,0,0.7); }
        100% { box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
    }
    .ruangan-header .grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }
    .kamar-card {
        background-color: #1f2937;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.5);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .kamar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.7);
    }
    .kamar-title {
        font-size: 2rem;
        margin-bottom: 20px;
        font-weight: bold;
        color: #e5e7eb;
        text-align: center;
    }
    .bed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 20px;
    }
    .bed-box {
        text-align: center;
        padding: 30px 0;
        border-radius: 15px;
        font-weight: bold;
        font-size: 1.5rem;
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transition: transform 0.3s, box-shadow 0.3s, filter 0.3s;
        position: relative;
    }
    .bed-box:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 12px 25px rgba(0,0,0,0.8);
        filter: brightness(1.2);
    }
    .bed-terisi {
        background: linear-gradient(145deg, #dc2626, #f87171);
    }
    .bed-kosong {
        background: linear-gradient(145deg, #16a34a, #4ade80);
    }
    .bed-box::after {
        content: '';
        display: block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .bed-terisi::after { background-color: #ff0000; }
    .bed-kosong::after { background-color: #00ff00; }
</style>
</head>
<body>

<h1>Dashboard Display Kamar RS</h1>

@foreach($getRuangan as $ruangan => $data)
    <!-- Header Ruangan -->
    <div class="ruangan-header">
        <div class="grid">
            <div>{{ $ruangan }}</div>
            <div>Total Bed: {{ $data['total_bed'] }}</div>
            <div>Terisi: {{ $data['total_isi'] }}</div>
            <div>Kosong: {{ $data['total_kosong'] }}</div>
        </div>
    </div>

    <!-- Daftar Kamar -->
    @foreach($data['kamar'] as $kamar => $info)
        <div class="kamar-card">
            <div class="kamar-title">{{ $kamar }} - Kelas: {{ $info['kelas'] }} ({{ $info['jumlah_isi'] }}/{{ count($info['beds']) }} Terisi)</div>

            <div class="bed-grid">
                @foreach($info['beds'] as $bed)
                    <div class="bed-box {{ $bed->status == 1 ? 'bed-terisi' : 'bed-kosong' }}">
                        Bed {{ $bed->bad }}<br>
                        {{ $bed->status == 1 ? 'Terisi' : 'Kosong' }}
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endforeach

</body>
</html>

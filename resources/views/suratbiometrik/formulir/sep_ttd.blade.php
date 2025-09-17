@extends('layout.layoutDashboard')

@section('title', 'Form Tanda Tangan SEP')

@section('konten')
<div class="container py-4">
    <h4 class="mb-3">Form Tanda Tangan SEP</h4>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Notifikasi error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sep.simpanTtd') }}">
        @csrf
        <div class="mb-3">
            <label for="no_sep" class="form-label">Nomor SEP</label>
            <input type="text" name="no_sep" id="no_sep" class="form-control"
                   value="{{ $sep->no_sep ?? '' }}" readonly>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Pasien</label>
            <input type="text" name="nama" id="nama" class="form-control"
                   value="{{ $sep->nm_pasien ?? '' }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanda Tangan Pasien / Keluarga</label>
            <div class="d-flex justify-content-center">
                <canvas id="signature-pad"
                    style="border:2px dashed #aaa; border-radius:10px; width:300px; height:300px; background:#fff;">
                </canvas>
            </div>
            <input type="hidden" name="ttd" id="ttd">
            <div class="mt-2 text-center">
                <button type="button" class="btn btn-secondary btn-sm" id="clear">Hapus</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

    {{-- Jika sudah ada tanda tangan tersimpan --}}
    @if(!empty($sep->ttd))
        <div class="mt-4">
            <h6>Tanda Tangan Tersimpan:</h6>
            <img src="{{ asset('storage/ttd/'.$sep->ttd) }}"
                 alt="TTD SEP"
                 style="max-width:300px; height:auto; border:1px solid #ddd; border-radius:8px;">
        </div>
    @endif
</div>

{{-- Signature Pad --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById("signature-pad");
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: "rgba(255,255,255,0)" // transparan
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = 300 * ratio;   // fix square 300x300
        canvas.height = 300 * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Tombol hapus
    document.getElementById("clear").addEventListener("click", () => signaturePad.clear());

    // Saat submit
    document.querySelector("form").addEventListener("submit", function(e) {
        if (signaturePad.isEmpty()) {
            alert("Silakan tanda tangani terlebih dahulu.");
            e.preventDefault();
        } else {
            document.getElementById("ttd").value = signaturePad.toDataURL("image/png");
        }
    });
</script>
@endsection

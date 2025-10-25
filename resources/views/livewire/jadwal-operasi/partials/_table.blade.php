@forelse ($jadwal_operasi ?? [] as $item)
<tr class="text-center animate__animated animate__fadeIn">
    <td>{{ $loop->iteration }}</td>
    <td>{{ $item->no_rawat }}</td>
    <td class="text-start">{{ $item->nm_pasien }}</td>
    <td>{{ $item->nm_dokter }}</td>
    <td>{{ $item->nm_ruang_ok }}</td>
    <td>{{ $item->nm_perawatan }}</td>
    <td>{{ $item->tanggal }}</td>
    <td>{{ $item->jam_mulai }}</td>
    <td>{{ $item->jam_selesai }}</td>
    <td>
        <span class="badge rounded-pill
            @if($item->status == 'Menunggu') bg-warning text-dark
            @elseif($item->status == 'Selesai') bg-success
            @else bg-secondary @endif">
            {{ strtoupper($item->status) }}
        </span>
    </td>
    <td>
        <form action="{{ route('jadwal.operasi.destroy', $item->no_rawat) }}" method="POST"
              onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="11" class="text-center text-muted py-4">Tidak ada jadwal operasi.</td>
</tr>
@endforelse

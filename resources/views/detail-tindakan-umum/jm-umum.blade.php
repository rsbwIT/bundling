@extends('..layout.layoutDashboard')
@section('title', 'JM Umum')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan-umum.component.cari-dokter')
            <div class="row no-print mb-2 mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped text-xs text-center w-100" style="white-space: nowrap; width: 100%;" id="tableToCopy">
                    <thead style="background-color: #b4a7d6; color: black;">
                    <tr>
                        <th rowspan="3" class="align-middle">No</th>
                        <th rowspan="3" class="align-middle">Kode</th>
                        <th rowspan="3" class="align-middle">KODE ID KHANZA</th>
                        <th rowspan="3" class="align-middle">Nama</th>
                        <th colspan="3" class="align-middle text-center">UMUM</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="align-middle text-center">UMUM</th>
                        <th rowspan="2" class="align-middle">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="align-middle">Rawat Inap</th>
                        <th class="align-middle">Rawat Jalan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($mappedTemplate as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->kode_template }}</td>
                            <td>{{ $item->kode_id_khanza }}</td>
                            <td class="text-left">{{ $item->nama_template }}</td>
                            <td class="text-right">{{ number_format($item->total_ranap) }}</td>
                            <td class="text-right">{{ number_format($item->total_ralan) }}</td>
                            <td class="text-right">{{ number_format($item->grand_total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Tidak ada data di rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background-color: #e9ecef; color: black; font-weight: bold;">
                    <tr>
                        <td colspan="4" class="text-right align-middle">TOTAL KESELURUHAN</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('total_ranap')) }}</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('total_ralan')) }}</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('grand_total')) }}</td>
                    </tr>
                </tfoot>
            </table>
            </div>


    </div>
    
    <style>
        /* Agar border tabel terlihat jelas seperti di Excel */
        /* Agar border tabel terlihat jelas seperti di Excel */
        #tableToCopy thead th {
            border: 1px solid #777;
        }
    </style>

    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId) {
            const table = document.getElementById(tableId);

            // Simplified header for copy: single row
            const copyHeaders = ['No', 'Kode', 'KODE ID KHANZA', 'Nama', 'Rawat Inap (Umum)', 'Rawat Jalan (Umum)', 'Total'];

            // --- Build plain text (tab-separated) with simplified header ---
            function getPlainText(table) {
                const lines = [];
                // Add simplified header row
                lines.push(copyHeaders.join('\t'));

                // Add tbody rows
                table.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const rowData = [];
                    cells.forEach(cell => {
                        rowData.push(cell.innerText.trim());
                    });
                    lines.push(rowData.join('\t'));
                });

                // Add tfoot rows
                table.querySelectorAll('tfoot tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const rowData = [];
                    cells.forEach(cell => {
                        const colspan = parseInt(cell.getAttribute('colspan')) || 1;
                        rowData.push(cell.innerText.trim());
                        for (let i = 1; i < colspan; i++) {
                            rowData.push('');
                        }
                    });
                    lines.push(rowData.join('\t'));
                });

                return lines.join('\n');
            }

            // --- Build HTML with inline styles and simplified header ---
            function getStyledHtml(table) {
                const clone = table.cloneNode(true);

                // Replace thead with single-row simplified header
                const thead = clone.querySelector('thead');
                thead.innerHTML = '';
                const headerRow = document.createElement('tr');
                copyHeaders.forEach(text => {
                    const th = document.createElement('th');
                    th.textContent = text;
                    th.style.border = '1px solid #777';
                    th.style.padding = '4px 8px';
                    th.style.whiteSpace = 'nowrap';
                    th.style.backgroundColor = '#b4a7d6';
                    th.style.color = 'black';
                    th.style.textAlign = 'center';
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);

                // Style body and footer cells
                clone.querySelectorAll('tbody td, tfoot td').forEach(cell => {
                    cell.style.border = '1px solid #777';
                    cell.style.padding = '4px 8px';
                    cell.style.whiteSpace = 'nowrap';
                });
                clone.querySelectorAll('tfoot td').forEach(td => {
                    td.style.backgroundColor = '#e9ecef';
                    td.style.color = 'black';
                    td.style.fontWeight = 'bold';
                });
                clone.style.borderCollapse = 'collapse';
                clone.style.fontSize = '12px';
                clone.style.fontFamily = 'Arial, sans-serif';
                return clone.outerHTML;
            }

            const plainText = getPlainText(table);
            const htmlContent = getStyledHtml(table);

            // Use Clipboard API to write both formats
            if (navigator.clipboard && navigator.clipboard.write) {
                const htmlBlob = new Blob([htmlContent], { type: 'text/html' });
                const textBlob = new Blob([plainText], { type: 'text/plain' });
                const clipboardItem = new ClipboardItem({
                    'text/html': htmlBlob,
                    'text/plain': textBlob
                });
                navigator.clipboard.write([clipboardItem]).then(() => {
                    alert("Tabel telah berhasil disalin ke clipboard.");
                }).catch(err => {
                    console.error("Tidak dapat menyalin tabel:", err);
                    // Fallback to old method
                    fallbackCopy(table);
                });
            } else {
                // Fallback for older browsers
                fallbackCopy(table);
            }
        }

        function fallbackCopy(table) {
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        }
    </script>
@endsection

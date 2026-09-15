<?php
$file = 'c:\xampp\htdocs\bundling2\resources\views\bpjs\bridginginacbg2.blade.php';
$lines = file($file);

$startIdx = -1;
$endIdx = -1;

for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], "@if(\$pasien->status_lanjut == 'Ranap' && \$triase)") !== false && $startIdx === -1 && $i > 700) {
        $startIdx = $i;
    }
    if (strpos($lines[$i], "<div class=\"modal fade\" id=\"modalEditDiagnosa\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">") !== false && $endIdx === -1 && $i > 1300) {
        $endIdx = $i;
    }
}

if ($startIdx !== -1 && $endIdx !== -1) {
    $before = array_slice($lines, 0, $startIdx);
    $after = array_slice($lines, $endIdx);
    
    $newContent = "
<div id=\"modalContainer\"></div>

<script>
function openTriaseModal(btn, norawat) {
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i>';
    btn.disabled = true;

    $.ajax({
        url: '" . '{{ route("inacbg.getTriaseModalHtml") }}' . "',
        type: 'GET',
        data: { norawat: norawat },
        success: function(response) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            $('#modalContainer').html(response.html);
            $('#modalTriase').modal('show');
        },
        error: function(xhr) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            var errMsg = 'Terjadi kesalahan sistem';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errMsg = xhr.responseJSON.error;
            }
            Swal.fire('Error', errMsg, 'error');
        }
    });
}

function openResumeModal(btn, norawat) {
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i>';
    btn.disabled = true;

    $.ajax({
        url: '" . '{{ route("inacbg.getResumeModalHtml") }}' . "',
        type: 'GET',
        data: { norawat: norawat },
        success: function(response) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            $('#modalContainer').html(response.html);
            $('#modalLihatResume').modal('show');
        },
        error: function(xhr) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            var errMsg = 'Terjadi kesalahan sistem';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errMsg = xhr.responseJSON.error;
            }
            Swal.fire('Error', errMsg, 'error');
        }
    });
}
</script>

";
    
    $finalContent = implode("", $before) . $newContent . implode("", $after);
    file_put_contents($file, $finalContent);
    echo "Successfully replaced lines " . ($startIdx + 1) . " to " . ($endIdx) . "!\n";
} else {
    echo "Could not find start or end index.\nStart: $startIdx, End: $endIdx\n";
}
?>

@php
// usage: @include('partials.avatar', ['nik' => $nik, 'id' => 'myId', 'class' => 'img-circle', 'style' => 'width:32px;height:32px;object-fit:cover'])
$nik = $nik ?? (session('user')->nik ?? null);
$sizeStyle = $style ?? '';
$extraClass = $class ?? '';
$idAttr = isset($id) ? 'id="'.$id.'"' : '';

$photoPath = null;
if($nik){
    $photoPath = DB::table('pegawai')->where('nik', $nik)->value('photo');
}

$src = asset('img/user.jpg');

if(!empty(session('user')->foto) && strpos(session('user')->foto, 'http') === 0){
    $candidate = session('user')->foto;
    $bn = basename($candidate);
    if(!empty($bn) && file_exists(public_path('uploads/pegawai/'.$bn))){
        $src = asset('uploads/pegawai/'.$bn);
    }else{
        try{ $h = @get_headers($candidate); if(is_array($h) && strpos($h[0],'200')!==false) $src = $candidate; }catch(\Throwable $e){}
    }
} elseif(!empty(session('user')->foto)){
    $sf = session('user')->foto;
    if(strpos($sf,'uploads/')===0 || file_exists(public_path($sf))){
        $src = asset($sf);
    }
}

if(($src === asset('img/user.jpg')) && !empty($photoPath) && $photoPath != 'pages/pegawai/photo/'){
    if(strpos($photoPath,'http')===0){
        $src = $photoPath;
    } elseif(strpos($photoPath,'uploads/')===0 || file_exists(public_path($photoPath)) || file_exists(public_path('uploads/pegawai/'.$photoPath))){
        if(strpos($photoPath,'uploads/')===0){
            $src = asset($photoPath);
        } elseif(file_exists(public_path($photoPath))){
            $src = asset($photoPath);
        } else {
            $src = asset('uploads/pegawai/'.$photoPath);
        }
    } else {
        $candidate = rtrim(env('URL_KHANZA',''),'/')."/webapps/penggajian/".$photoPath;
        try{ $h = @get_headers($candidate); if(is_array($h) && strpos($h[0],'200')!==false) $src = $candidate; }catch(\Throwable $e){}
    }
}
@endphp

<img {!! $idAttr !!} src="{{ $src }}" class="{{ $extraClass }}" style="{{ $sizeStyle }}" alt="Avatar">
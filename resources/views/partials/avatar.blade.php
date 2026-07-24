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

if(!empty(session('user')->foto)){
    $sf = session('user')->foto;
    if (strpos($sf, 'http') === 0) {
        $src = $sf;
    } elseif (strpos($sf, '/') === 0) {
        $src = $sf;
    } elseif (strpos($sf, 'pages/pegawai/photo/') === 0) {
        $src = rtrim(env('URL_KHANZA',''),'/')."/webapps/penggajian/".$sf;
    } else {
        $src = asset($sf);
    }
}

if(($src === asset('img/user.jpg')) && !empty($photoPath) && $photoPath != 'pages/pegawai/photo/'){
    if (strpos($photoPath, 'http') === 0) {
        $src = $photoPath;
    } elseif (strpos($photoPath, '/') === 0) {
        $src = $photoPath;
    } elseif (strpos($photoPath, 'uploads/') === 0) {
        $parsed = parse_url(asset($photoPath));
        $src = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?'.$parsed['query'] : '');
    } else {
        $src = rtrim(env('URL_KHANZA',''),'/')."/webapps/penggajian/".$photoPath;
    }
}
@endphp

<img {!! $idAttr !!} src="{{ $src }}" class="{{ $extraClass }}" style="{{ $sizeStyle }}" alt="Avatar">
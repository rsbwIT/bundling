class RegPeriksa extends Model
{
public function dokter()
{
return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
}

public function loket()
{
return $this->belongsTo(Loket::class, 'id_loket', 'id');
}
}
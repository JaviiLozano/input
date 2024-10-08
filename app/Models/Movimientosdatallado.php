<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Movimientosdatallado
 *
 * @property $id
 * @property $Movimientos_id
 * @property $Producto_id
 * @property $Cantidad_Ingreso
 * @property $ValorUnitario
 * @property $TotalValor
 * @property $Impuesto_id
 * @property $Cantidad_Egreso
 * @property $Valor_Unitario
 * @property $users_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Impuesto $impuesto
 * @property Movimiento $movimiento
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Movimientosdatallado extends Model
{
    
    static $rules = [
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['Movimientos_id','Impuesto','Descuento','Producto_id','Cantidad_Ingreso','ValorUnitario','TotalValor','Impuesto_id','Cantidad_Egreso','Valor_Unitario','Obervacion','users_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function movimiento()
    {
        return $this->hasOne('App\Models\Movimiento', 'id', 'Movimientos_id');
    }
    
    public function products()
    {
        return $this->hasOne('App\Models\Producto', 'id', 'Producto_id');
    }
    
    public function productos()
    {
        return $this->belongsTo('App\Models\Producto', 'Producto_id', 'id');
    }
    

}

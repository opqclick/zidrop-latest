<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model {
    protected $fillable = ['invoiceNo', 'recipientName', 'recipientAddress', 'recipientPhone', 'merchantId', 'merchantAmount', 'merchantDue', 'cod', 'productWeight', 'note', 'trackingCode', 'deliveryCharge', 'codCharge', 'orderType', 'codType', 'percelType', 'status', 'reciveZone', 'pay_return'];

    public function deliverymen() {
        return $this->hasOne('App\Deliveryman', 'id', 'deliveryman');
    }

    public function merchant() {
        return $this->belongsTo('App\Merchant', 'merchantId');
    }

    public function parceltype() {
        return $this->belongsTo('App\Parceltype', 'status');
    }

    public function division() {
        return $this->belongsTo(Division::class);
    }

    public function district() {
        return $this->belongsTo(UpDistrict::class, 'up_district_id', 'id');
    }

    public function area() {
        return $this->belongsTo(Area::class);
    }

    public function union() {
        return $this->belongsTo(Nearestzone::class, 'reciveZone', 'id');
    }

    public function parcelnote() {
        return $this->hasOne(Parcelnote::class, 'parcelId')->orderBy('id','desc');
    }

}
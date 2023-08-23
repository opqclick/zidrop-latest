<div class="row">
    <div class="col-sm-7">
        <p>Cash Collection</p>
    </div>
    <div class="col-sm-5">
        <p>{{ number_format(Session::get('codpay'), 2) }} N</p>
    </div>
</div>
<!-- row end -->
<div class="row">
    <div class="col-sm-7">
        <p>Delivery Charge</p>
    </div>
    <div class="col-sm-5">
        <p>{{ number_format(Session::get('pdeliverycharge'), 2) }} N</p>
    </div>
</div>
<!-- row end -->
<div class="row">
    <div class="col-sm-7">
        <p>Cod Charge</p>
    </div>
    <div class="col-sm-5">
        <p>{{ number_format(Session::get('pcodecharge'), 2) }} N</p>
    </div>
</div>
<!-- row end -->
<div class="row total-bar">
    <div class="col-sm-7">
        <p>Total Payable Amount</p>
    </div>
    <div class="col-sm-5">
        <p>{{ number_format(Session::get('codpay') - (Session::get('pdeliverycharge') + Session::get('pcodecharge')), 2) }}
            N</p>
    </div>
</div>
<!-- row end -->
<div class="row">
    <div class="col-sm-12">
        <p class="text-center">Note : <span class="">If you request for pick up after 5pm , it will be picked up
                the next day</span></p>
    </div>
</div>
<!-- row end -->

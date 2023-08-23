<table>
    <thead>
    <tr>
        <th>Beneficiary Bank Code</th>
        <th>Beneficiary Account Number</th>
        <th>Beneficiary Name</th>
        <th>Amount</th>
        <th>Narration(Must be between 5 to 47 Characters) </th>
    </tr>
    </thead>

    <tbody>
    @foreach ($show_data as $key => $value)
        @php
            $due = 0;
            foreach ($value->parcels as $parcel) {
                if ($parcel->status == 4 || $parcel->status == 6 || $parcel->status == 10) {
                    // $due = $due + ($parcel->codCharge + $parcel->deliveryCharge - $parcel->cod);
                    $due = $due + $parcel->merchantDue;
                }
            }
        @endphp
        @if ($due > 0)
            <tr>
                <td>{{ $value->beneficiary_bank_code }}</td>
                <td>{{ $value->bankAcNo }}</td>
                <td>{{ $value->companyName }}</td>
                <td>{{ $due }}</td>
                <td>SETTLEMENT</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
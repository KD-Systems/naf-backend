<table>
    <thead>
        <tr>
            @if ($filters['year'])
                <th colspan="2"
                    style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">
                    Year:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['year'] }}
                </td>
            @endif
            @if ($filters['month'])
                <th
                    style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">
                    Month:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">
                    {{ $filters['month'] }}</td>
            @endif
            @if ($filters['company_id'])
                <th
                    style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">
                    Company:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">
                    {{ $filters['company'] }}</td>
            @endif
            @if ($filters['q'])
                <th
                    style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">
                    Search:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['q'] }}
                </td>
            @endif
        </tr>
        <tr>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="150px">ID</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="150px">Date</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="300px">Quoted Company Name</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="200px">Cash Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="200px">Cheque Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="200px">Bank Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="200px">Advance Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="200px">Credit Sale (DR)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="150px">Due Collection</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;"
                width="600px">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $invoice)
            @php
                $dueCollection = $invoice->payment_mode == 'due_collection' ? $invoice->grand_total : 0;
                $credit = $invoice->payment_mode == 'credit' ? $invoice->grand_total : 0;
                $cash = $invoice->payment_mode == 'cash' ? $invoice->grand_total : 0;
                $check = $invoice->payment_mode == 'check' ? $invoice->grand_total : 0;
                $bank = $invoice->payment_mode == 'bank' ? $invoice->grand_total : 0;
                $advance = $invoice->payment_mode == 'advance' ? $invoice->grand_total : 0;
            @endphp
            <tr>
                <td style="height: 40px; text-align: center; vertical-align: middle;">#{{ $invoice->invoice_number }}
                </td>
                <td style="height: 40px; text-align: center; vertical-align: middle;">
                    {{ $invoice->created_at->format('Y-m-d') }}
                </td>
                <td style="height: 40px; vertical-align: middle;">{{ $invoice->company_name }}</td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $cash ? number_format($cash) . ' BDT' : 0 }}</td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $check ? number_format($check) . ' BDT' : 0 }}</td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $bank ? number_format($bank) . ' BDT' : 0 }}</td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $advance ? number_format($advance) . ' BDT' : 0 }}</td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $credit ? number_format($credit) . ' BDT' : 0 }}
                </td>
                <td style="height: 40px; vertical-align: middle; text-align: right">
                    {{ $dueCollection ? number_format($dueCollection) . ' BDT' : 0 }}
                </td>
                <td style="height: 40px; vertical-align: middle; word-wrap:break-all;">
                    {{ $invoice->remarks }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="height: 40px; vertical-align: middle; text-align: right">
                {{ number_format($data->where('payment_mode', 'cash')->sum('grand_total')) }} BDT</td>
            <td style="height: 40px; vertical-align: middle; text-align: right">
                {{ number_format($data->where('payment_mode', 'check')->sum('grand_total')) }} BDT</td>
            <td style="height: 40px; vertical-align: middle; text-align: right">
                {{ number_format($data->where('payment_mode', 'bank')->sum('grand_total')) }} BDT</td>
            <td style="height: 40px; vertical-align: middle; text-align: right">
                {{ number_format($data->where('payment_mode', 'advance')->sum('grand_total')) }} BDT</td>
            <td style="height: 40px; vertical-align: middle; text-align: right">{{ number_format($data->where('payment_mode', 'credit')->sum('grand_total')) }} BDT</td>
            <td style="height: 40px; vertical-align: middle; text-align: right">{{ number_format($data->where('payment_mode', 'due_collection')->sum('grand_total')) }} BDT</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3"
                style="height: 40px; vertical-align: middle; text-align: right; background-color:#b4fac7; border: 1px solid gray; font-weight: bold;">
                Grand Total</td>
            <td colspan="6"
                style="height: 40px; vertical-align: middle; text-align: center; background-color:#b4fac7; border: 1px solid gray; font-weight: bold;">
                {{ number_format($data->sum('grand_total')) }}
                BDT</td>
            <td style="background-color:#b4fac7; border: 1px solid gray;"></td>
        </tr>
    </tbody>
</table>

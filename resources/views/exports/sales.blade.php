<table>
    <thead>
        <tr>
            @if($filters['year'])
                <th colspan="2" style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">Year:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['year'] }}</td>
            @endif
            @if($filters['month'])
                <th style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">Month:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['month'] }}</td>
            @endif
            @if($filters['company_id'])
                <th style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">Company:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['company'] }}</td>
            @endif
            @if($filters['q'])
                <th style="width: 100px; height: 40px; vertical-align: middle; background-color:#faefb4; border: 1px solid gray; text-align: center">Search:</th>
                <td style="width: 100px; height: 40px; vertical-align: middle; text-align: center">{{ $filters['q'] }}</td>
            @endif
        </tr>
        <tr>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="30px">SL</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Quotation Date</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="300px">Quoted Company Name</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Cash Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Cheque Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Bank Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Advance Sale (Credit Transaction)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="150px">Credit Sale (DR)</th>
            <th style="height: 40px; text-align: center; background-color: #faefb4; border: 1px solid gray; vertical-align: middle;" width="600px">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @php($credit = 0)
        @foreach ($data as $i => $invoice)
        @if(!$invoice->advance_amount && !$invoice->cash_amount && !$invoice->check_amount && !$invoice->bank_amount)
            @php($credit += $invoice->grand_total)
        @endif

            <tr>
                <td style="min-height: 40px; text-align: center; vertical-align: top;">{{ $i++ }}</td>
                <td style="min-height: 40px; text-align: center; vertical-align: top;">
                    @foreach (explode(',', $invoice->invoice_dates) as $date)
                    {{ $date }} <br/>
                    @endforeach
                </td>
                <td style="min-height: 40px; vertical-align: top;">{{ $invoice->company_name }}</td>
                <td style="min-height: 40px; vertical-align: top; text-align: right">{{ $invoice->cash_amount ? number_format($invoice->cash_amount) . ' BDT' : 0 }}</td>
                <td style="min-height: 40px; vertical-align: top; text-align: right">{{ $invoice->check_amount ? number_format($invoice->check_amount) . ' BDT' : 0 }}</td>
                <td style="min-height: 40px; vertical-align: top; text-align: right">{{ $invoice->bank_amount ? number_format($invoice->bank_amount) . ' BDT' : 0 }}</td>
                <td style="min-height: 40px; vertical-align: top; text-align: right">{{ $invoice->advance_amount ? number_format($invoice->advance_amount) . ' BDT' : 0 }}</td>
                <td style="min-height: 40px; vertical-align: top; text-align: right">
                    {{ !$invoice->advance_amount && !$invoice->cash_amount && !$invoice->check_amount && !$invoice->bank_amount && $invoice->grand_total ? number_format($invoice->grand_total) . 'BDT' : 0 }}
                </td>
                <td style="min-height: 40px; vertical-align: top; word-wrap:break-all;">
                    @foreach (explode('/n', $invoice->remarks) as $remark)
                    {{ trim($remark, ',') }}<br/>
                    @endforeach
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="height: 40px; vertical-align: top; text-align: right">{{ number_format($data->sum('cash_amount')) }} BDT</td>
            <td style="height: 40px; vertical-align: top; text-align: right">{{ number_format($data->sum('check_amount')) }} BDT</td>
            <td style="height: 40px; vertical-align: top; text-align: right">{{ number_format($data->sum('bank_amount')) }} BDT</td>
            <td style="height: 40px; vertical-align: top; text-align: right">{{ number_format($data->sum('advance_amount')) }} BDT</td>
            <td style="height: 40px; vertical-align: top; text-align: right">{{ number_format($credit) }} BDT</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="7" style="height: 40px; vertical-align: top; text-align: right; background-color:#b4fac7; border: 1px solid gray;">Grand Total</td>
            <td style="height: 40px; vertical-align: top; text-align: right; background-color:#b4fac7; border: 1px solid gray;">{{ number_format($data->sum('cash_amount') + $data->sum('check_amount') + $data->sum('bank_amount') + $data->sum('advance_amount') + $credit) }} BDT</td>
            <td style="background-color:#b4fac7; border: 1px solid gray;"></td>
        </tr>
    </tbody>
</table>

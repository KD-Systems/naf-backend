<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Quotation Date</th>
            <th>Quoted Company Name</th>
            <th>Cash Sale (Credit Transaction)</th>
            <th>Cheque Sale (Credit Transaction)</th>
            <th>Credit Sale (DR)</th>
            <th>Naf Sale (Debit Transaction)</th>
            <th>Due Collection</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $invoice)
        <tr>
            <td>{{ $i++ }}</td>
            <td>{{ \Carbon\Carbon::create($invoice->created_at)->format('d.m.Y') }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
            <td>{{ $invoice->company_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

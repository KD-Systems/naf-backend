@component('mail::message')
# New Invoice: `#{{$invoice->invoice_number}}`

Dear Concern,

A new Invoice has created by `{{$user->name}}`, please take a look. To view the invoice, click the below button.

@component('mail::button', ['url' => $url])
View Invoice
@endcomponent

Thank you
@endcomponent

@component('mail::message')
# New Invoice: `#{{$deliveryNote->dn_number}}`

Dear Concern,

A new Delivery has created by `{{$user->name}}`, please take a look. To view the invoice, click the below button.

@component('mail::button', ['url' => $url])
View Delivery Note
@endcomponent

Thank you
@endcomponent

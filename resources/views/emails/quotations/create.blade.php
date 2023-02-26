@component('mail::message')
# New Quotation: `#{{$quotation->pq_number}}`

Dear Concern,

A new quotation has created by `{{$user->name}}`, please take a look. To view the quotation, click the below button.

@component('mail::button', ['url' => $url])
View Quotation
@endcomponent

Thank you
@endcomponent

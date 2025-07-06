<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('app.invoice') {{ $invoice->filename }}</title>
    <style>

        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew_Bold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew_Bold_Italic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew_Italic.ttf') }}") format('truetype');
        }

        @if($globalInvoiceSetting->is_chinese_lang)
        @font-face {
            font-family: SimHei;
            /*font-style: normal;*/
            font-weight: bold;
            src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
        }
        @endif

        @php
            $font = '';
            if($globalInvoiceSetting->locale == 'ja') {
                $font = 'ipag';
            } else if($globalInvoiceSetting->locale == 'hi') {
                $font = 'hindi';
            } else if($globalInvoiceSetting->locale == 'th') {
                $font = 'THSarabunNew';
            } else if($globalInvoiceSetting->is_chinese_lang) {
                $font = 'SimHei';
            }else {
                $font = 'Verdana';
            }
        @endphp

        @if($globalInvoiceSetting->is_chinese_lang)
            body
        {
            font-weight: normal !important;
        }
        @endif
        * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            text-decoration: none;
        }

        body {
            position: relative;
            width: 100%;
            height: auto;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 13px;
            /* font-family: Verdana, Arial, Helvetica, sans-serif; */
        }

        h2 {
            font-weight: normal;
        }

        header {
            padding: 10px 0;
        }

        #logo img {
            height: 50px;
            margin-bottom: 15px;
        }

        #details {
            margin-bottom: 25px;
        }

        #client {
            padding-left: 6px;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.2em;
            font-weight: normal;
            margin: 0;
        }

        #invoice h1 {
            color: #0087C3;
            line-height: 2em;
            font-weight: normal;
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            /* margin-bottom: 20px; */
        }

        table th,
        table td {
            padding: 5px 8px;
            text-align: center;
        }

        table th {
            background: #EEEEEE;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td.desc h3,
        table td.qty h3 {
            font-size: 0.9em;
            font-weight: normal;
            margin: 0 0 0 0;
        }

        table .no {
            font-size: 1.2em;
            width: 10%;
            text-align: center;
            border-left: 1px solid #e7e9eb;
        }

        table .desc,
        table .item-summary {
            text-align: left;
        }

        table .unit {
            /* background: #DDDDDD; */
            border: 1px solid #e7e9eb;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
            text-align: center;
        }

        table td.unit {
            width: 35%;
        }

        table td.desc {
            width: 45%;
        }

        table td.qty {
            width: 5%;
        }

        .status {
            margin-top: 15px;
            padding: 1px 8px 5px;
            font-size: 1.3em;
            width: 80px;
            float: right;
            text-align: center;
            display: inline-block;
        }

        .status.unpaid {
            background-color: #E7505A;
        }

        .status.paid {
            background-color: #26C281;
        }

        .status.cancelled {
            background-color: #95A5A6;
        }

        .status.error {
            background-color: #F4D03F;
        }

        table tr.tax .desc {
            text-align: right;
        }

        table tr.discount .desc {
            text-align: right;
            color: #E43A45;
        }

        table tr.subtotal .desc {
            text-align: right;
        }


        table tfoot td {
            padding: 10px;
            font-size: 1.2em;
            white-space: nowrap;
            border-bottom: 1px solid #e7e9eb;
            font-weight: 700;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr td:first-child {
            /* border: none; */
        }


        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #e7e9eb;
            padding: 8px 0;
            text-align: center;
        }

        table.billing td {
            background-color: #fff;
        }

        table td#invoiced_to {
            text-align: left;
            padding-left: 0;
        }

        #notes {
            color: #767676;
            font-size: 11px;
        }

        .item-summary {
            font-size: 11px;
            padding-left: 0;
        }


        .page_break {
            page-break-before: always;
        }


        table td.text-center {
            text-align: center;
        }

        .word-break {
            word-wrap: break-word;
        }

        #invoice-table td {
            border: 1px solid #e7e9eb;
        }

        .border-left-0 {
            border-left: 0 !important;
        }

        .border-right-0 {
            border-right: 0 !important;
        }

        .border-top-0 {
            border-top: 0 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }

        @if($globalInvoiceSetting->locale == 'th')

            table td {
            font-weight: bold !important;
            font-size: 20px !important;
            }

            .description
            {
                font-weight: bold !important;
                font-size: 16px !important;
            }
        @endif

        .client-logo {
            height:50px;
            margin-bottom:20px;
        }

    </style>
</head>

<body>
    <header class="clearfix"  class="description">

        <table cellpadding="0" cellspacing="0" class="billing">
            <tr>
                <td colspan="2">
                    <h1>@lang('app.invoice')</h1>
                </td>
            </tr>
            <tr>
                <td id="invoiced_to">
                    <div  class="description">
                        <div class="client-logo-div">
                            <img src="{{ $company->logo_url }}" alt="{{ $company->company_name }}" class="client-logo"/>
                        </div>
                    <small>@lang('modules.invoices.billedTo'):</small><br>
                    {{ $company->company_name }}<br>
                    {!! nl2br($company->address) !!}
                    </div>
                </td>
                <td>
                    <div id="company"  class="description">
                        <div id="logo">
                            <img src="{{ $globalInvoiceSetting->logo_url }}" alt="home" class="dark-logo" />
                        </div>
                        <small>@lang('modules.invoices.billedFrom'):</small>
                        <div>{{ $globalInvoiceSetting->billing_name }}</div>
                        @if($superadmin->company_phone)<div>{{ $superadmin->company_phone }}</div>@endif

                        <div>{!! nl2br($globalInvoiceSetting->billing_address) !!}</div>
                        @if(!is_null($globalInvoiceSetting->billing_tax_name))<div>{{ $globalInvoiceSetting->billing_tax_name }}: {{ $globalInvoiceSetting->billing_tax_id }}</div>@endif
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <main>
        <div id="details">
            <div id="invoice"  class="description">
                <h1># {{ $invoice->invoice_number }}</h1>

                <div class="date">@lang('superadmin.issue_date'):
                    {{  !is_null($invoice->pay_date) ? $invoice->pay_date->translatedFormat($global->date_format) : $invoice->created_at->translatedFormat($global->date_format)}}
                </div>
            </div>
        </div>
        <table cellspacing="0" cellpadding="0" id="invoice-table">
            <thead>
                <tr>
                    <th class="no description">#</th>
                    <th class="desc description">@lang("app.description")</th>
                    <th class="desc description">@lang("app.date")</th>
                    <th class="unit description">@lang("app.amount") ({!! htmlentities($invoice->currency->currency_code) !!})</th>
                </tr>
            </thead>
            <tbody>
                <tr style="page-break-inside: avoid;">
                    <td class="no">1</td>
                    <td class="desc">
                        <h3>{{ $invoice->package->name  }} @if($invoice->package->default != 'trial' || && $invoice->package->default != 'lifetime') - @lang('superadmin.'.$invoice->company->package_type) @endif </h3>
                    </td>
                    <td class="desc">
                        <h3>{{ $invoice->pay_date?->format($global->date_format) }} @if($invoice->next_pay_date) - {{ $invoice->next_pay_date->format($global->date_format) }} @endif
                        </h3>
                    </td>
                    <td class="unit">@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
                <tr style="page-break-inside: avoid;" class="subtotal">
                    <td colspan="3" class="desc">@lang('modules.invoices.subTotal')</td>
                    <td class="unit">@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr dontbreak="true">
                    <td colspan="3">
                        @lang('modules.invoices.total')</td>
                    <td style="text-align: center">@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>

                @if ($globalInvoiceSetting->authorised_signatory && $globalInvoiceSetting->authorised_signatory_signature)
                    <tr>
                        <td colspan="{{ $globalInvoiceSetting->hsn_sac_code_show ? '7' : '6' }}" style="font-size:15px; border: 0" align="right">
                            <img style="height:95px; margin-bottom: -50px; margin-top: 5px;"
                            src="{{ $globalInvoiceSetting->authorised_signatory_signature_url }}" alt="{{ $global->company_name }}"/><br><br>
                            <p style="margin-top: 25px;">@lang('modules.invoiceSettings.authorisedSignatory')</p>
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>

        <p id="notes" class="word-break description">
            @lang('modules.invoiceSettings.invoiceTerms') <br>{!! nl2br($globalInvoiceSetting->invoice_terms) !!}
        </p>
    </main>
</body>

</html>

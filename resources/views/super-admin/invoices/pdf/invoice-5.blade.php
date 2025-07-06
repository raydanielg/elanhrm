<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@lang('app.invoice') {{ $invoice->filename }}</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $global->favicon_url }}">
    <meta name="theme-color" content="#ffffff">

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

        @if ($globalInvoiceSetting->is_chinese_lang)
            @font-face {
                font-family: SimHei;
                /*font-style: normal;*/
                font-weight: bold;
                src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
            }

        @endif

        @php $font='';

        if($globalInvoiceSetting->locale=='ja') {
            $font='ipag';
        }

        else if($globalInvoiceSetting->locale=='hi') {
            $font='hindi';
        }

        else if($globalInvoiceSetting->locale=='th') {
            $font='THSarabunNew';
        }

        else if($globalInvoiceSetting->is_chinese_lang) {
            $font='SimHei';
        }

        else {
            $font='Verdana';
        }
        @endphp

        @if ($globalInvoiceSetting->is_chinese_lang)
            body {
                font-weight: normal !important;
            }

        @endif
        * {
            font-family: {{ $font }}, DejaVu Sans, sans-serif;
        }


        .bg-grey {
            background-color: #F2F4F7;
        }

        .bg-white {
            background-color: #fff;
        }

        .border-radius-25 {
            border-radius: 0.25rem;
        }

        .p-25 {
            padding: 1.25rem;
        }

        .f-11 {
            font-size: 11px;
        }

        .f-13 {
            font-size: 13px;
        }

        .f-14 {
            font-size: 13px;
        }

        .f-15 {
            font-size: 13px;
        }

        .f-21 {
            font-size: 17px;
        }

        .text-black {
            color: #28313c;
        }

        .text-grey {
            color: #616e80;
        }

        .font-weight-700 {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .line-height {
            line-height: 20px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .b-collapse {
            border-collapse: collapse;
        }

        .heading-table-left {
            padding: 6px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            border-right: 0;
        }

        .heading-table-right {
            padding: 6px;
            border: 1px solid #DBDBDB;
            border-left: 0;
        }

        .unpaid {
            color: #d30000;
            border: 1px solid #d30000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .other {
            color: #000000;
            border: 1px solid #000000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 120px;
            text-align: center;
            margin-top: 50px;
        }

        .paid {
            color: #28a745 !important;
            border: 1px solid #28a745;
            position: relative;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .main-table-heading {
            border: 1px solid #DBDBDB;
            background-color: #f1f1f3;
            font-weight: 700;
        }

        .main-table-heading td {
            padding: 5px 8px;
            border: 1px solid #DBDBDB;
        }

        .main-table-items td {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
        }

        .total-box {
            border: 1px solid #e7e9eb;
            padding: 0px;
            border-bottom: 0px;
        }

        .subtotal {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .subtotal-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .total {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
            border-left: 0;
            border-right: 0;
        }

        .total-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            font-weight: 700;
        }

        .balance {
            font-size: 14px;
            font-weight: bold;
            background-color: #f1f1f3;
        }

        .balance-left {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .balance-right {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .centered {
            margin: 0 auto;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .leftaligned {
            margin-left: 0;
            margin-right: auto;
        }

        .page_break {
            page-break-before: always;
        }

        #logo {
            height: 50px;
        }

        .word-break {
            max-width: 175px;
            word-wrap: break-word;
        }

        .summary {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            font-size: 11px;
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

        @if ($globalInvoiceSetting->locale == 'th')

            table td {
                font-weight: bold !important;
                font-size: 20px !important;
            }

            .description {
                font-weight: bold !important;
                font-size: 16px !important;
            }

        @endif
    </style>
</head>

<body class="content-wrapper">
<table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
    <tbody>
    <!-- Table Row Start -->
    <tr>
        <td>
            <img src="{{ $globalInvoiceSetting->logo_url }}" alt="{{ $globalInvoiceSetting->billing_name }}" id="logo"/>
        </td>
        <td align="right" class="f-21 text-black font-weight-700 text-uppercase">@lang('app.invoice')<br>
            <table class="text-black mt-1 f-13 b-collapse rightaligned">
                <tr>
                    <td class="heading-table-left">@lang('superadmin.invoiceNo')</td>
                    <td class="heading-table-right">{{ $invoice->invoice_number }}</td>
                </tr>
                    <tr>
                        <td class="heading-table-left">@lang('modules.invoices.invoiceDate')</td>
                        <td class="heading-table-right">
                            {{ !is_null($invoice->pay_date) ? $invoice->pay_date->translatedFormat($company->date_format) : $invoice->created_at->translatedFormat($company->date_format) }}
                        </td>
                    </tr>

            </table>
        </td>
    </tr>
    <!-- Table Row End -->
    <!-- Table Row Start -->
    <tr>
        <td class="f-14 text-black">
            <p class="line-height mb-0 ">
                <span class="text-grey text-capitalize">@lang('modules.invoices.billedFrom')</span><br>
                {{ $globalInvoiceSetting->billing_name }}<br>
                @if($superadmin->company_phone){{ $superadmin->company_phone }}<br>@endif
                {!! nl2br($globalInvoiceSetting->billing_address) !!}<br>
                @if(!is_null($globalInvoiceSetting->billing_tax_name))
                    {{ $globalInvoiceSetting->billing_tax_name }}: {{ $globalInvoiceSetting->billing_tax_id }}
                    <br>
                @endif
            </p>
        </td>
        <td class="f-14 text-black" align="right">
            <p class="line-height mb-0">
                        <span class="text-grey text-capitalize">@lang('modules.invoices.billedTo')</span>
                    <br>
                {{ $company->company_name }}<br>
                {!! nl2br($company->address) !!}

            </p>
        </td>
    </tr>
    <!-- Table Row End -->
    </tbody>
</table>

<table width="100%" class="f-14 b-collapse">
    <tr>
        <td height="10" colspan="2"></td>
    </tr>
    <!-- Table Row Start -->
    <tr class="main-table-heading text-grey">
        <td>#</td>
        <td width="40%">@lang('app.description')</td>
        <td align="right">@lang("app.date")</td>
        <td align="right">@lang("app.amount") ({!! htmlentities($invoice->currency->currency_code) !!})</td>
    </tr>
    <!-- Table Row End -->

        <!-- Table Row Start -->
            <tr class="main-table-items text-black">
                <td width="20px">1</td>
                <td width="40%" class="border-bottom-0">
                    {{ $invoice->package->name  }} @if($invoice->package->default != 'trial' && $invoice->package->default != 'lifetime') - @lang('superadmin.'.$company->package_type) @endif
                </td>
                <td align="right"
                    class="border-bottom-0">{{ $invoice->pay_date?->format($global->date_format) }} @if($invoice->next_pay_date) - {{ $invoice->next_pay_date->format($global->date_format) }} @endif</td>
                <td align="right" class="border-bottom-0">@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
            </tr>
            <!-- Table Row End -->
        </table>

<table width="100%" class="f-14 b-collapse">
    <tr>
        <td class="total-box" align="right" colspan="3">
            <table width="100%" border="0" class="b-collapse">
                <!-- Table Row Start -->
                <tr align="right" class="text-grey">
                    <td width="50%" class="subtotal">@lang('modules.invoices.subTotal')</td>
                </tr>
                <!-- Table Row End -->
            <!-- Table Row Start -->
                <tr align="right" class="text-grey">
                    <td width="50%" class="total">@lang('modules.invoices.total')</td>
                </tr>
                <!-- Table Row End -->

            </table>
        </td>
        <td class="total-box" align="right"
            width="20%">
            <table width="100%" class="b-collapse">
                <!-- Table Row Start -->
                <tr align="right" class="text-grey">
                    <td class="subtotal-amt">
                        @if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
                <!-- Table Row End -->
            <!-- Table Row Start -->
                <tr align="right" class="text-grey">
                    <td class="total-amt f-15">
                        @if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
                <!-- Table Row End -->
            </table>
        </td>
    </tr>
</table>

<table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
    <tbody>
    <tr class="text-grey">
        @if ($globalInvoiceSetting->authorised_signatory && $globalInvoiceSetting->authorised_signatory_signature)
            <td class="" align="right">
                <img style="height:95px; margin-bottom: -50px; margin-top: 15px;"
                    src="{{ $globalInvoiceSetting->authorised_signatory_signature_url }}"
                    alt="{{ $global->company_name }}"
                    id="logo"/><br><br>
                <p style="margin-top: 25px;">@lang('modules.invoiceSettings.authorisedSignatory')</p>
            </td>
        @endif

    </tr>
    <!-- Table Row Start -->
    @if (!($globalInvoiceSetting->authorised_signatory && $globalInvoiceSetting->authorised_signatory_signature))
        <tr>
            <td height="10"></td>
        </tr>
    @endif
    <tr>
        <td class="f-11">
            @lang('modules.invoiceSettings.invoiceTerms')</td>
    </tr>
    <!-- Table Row End -->
    <!-- Table Row Start -->
    <tr class="text-grey">
        <td class="f-11 line-height">{!! nl2br($globalInvoiceSetting->invoice_terms) !!}</td>
    </tr>
    </tbody>
</table>


</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@lang('app.invoice') {{ $invoice->filename }}</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Invoice">


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

        /*! Invoice Templates @author: Invoicebus @email: info@invoicebus.com @web: https://invoicebus.com @version: 1.0.0 @updated: 2015-02-27 16:02:34 @license: Invoicebus */
        /* Reset styles */
        /*@import url("https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=cyrillic,cyrillic-ext,latin,greek-ext,greek,latin-ext,vietnamese");*/
        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        b,
        u,
        i,
        center,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td,
        article,
        aside,
        canvas,
        details,
        embed,
        figure,
        figcaption,
        footer,
        header,
        hgroup,
        menu,
        nav,
        output,
        ruby,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            /* font-family: Verdana, Arial, Helvetica, sans-serif; */
            vertical-align: baseline;
        }

        html {
            line-height: 1;
        }

        ol,
        ul {
            list-style: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        caption,
        th,
        td {
            text-align: left;
            font-weight: normal;
            vertical-align: middle;
        }

        q,
        blockquote {
            quotes: none;
        }

        q:before,
        q:after,
        blockquote:before,
        blockquote:after {
            content: "";
            content: none;
        }

        a img {
            border: none;
        }

        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        main,
        menu,
        nav,
        section,
        summary {
            display: block;
        }

        /* Invoice styles */
        /**
         * DON'T override any styles for the <html> and <body> tags, as this may break the layout.
         * Instead wrap everything in one main <div id="container"> element where you may change
         * something like the font or the background of the invoice
         */
        html,
        body {
            /* MOVE ALONG, NOTHING TO CHANGE HERE! */
        }

        /**
         * IMPORTANT NOTICE: DON'T USE '!important' otherwise this may lead to broken print layout.
         * Some browsers may require '!important' in oder to work properly but be careful with it.
         */
        .clearfix {
            display: block;
            clear: both;
        }

        .hidden {
            display: none;
        }

        b,
        strong,
        .bold {
            font-weight: bold;
        }

        #container {
            font: normal 13px/1.4em 'Open Sans', Sans-serif;
            margin: 0 auto;
            color: #5B6165;
            position: relative;
        }

        #memo {
            padding-top: 40px;
            margin: 0 30px;
            border-bottom: 1px solid #ddd;
            height: 85px;
        }

        #memo .logo {
            float: left;
            margin-right: 20px;
        }

        #memo .logo img {
            height: 50px;
        }

        #memo .company-info {
            /*float: right;*/
            text-align: right;
            line-height: 18px;
        }

        #memo .company-info>div:first-child {
            line-height: 1em;
            font-size: 20px;
            color: #B32C39;
        }

        #memo .company-info span {
            font-size: 11px;
            display: inline-block;
            min-width: 20px;
            width: 100%;
        }

        #memo:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number {
            font-weight: bold;
            margin: 30px 0;
        }

        #invoice-title-number span {
            line-height: 0.88em;
            display: inline-block;
            min-width: 20px;
        }

        #invoice-title-number #title {
            text-transform: uppercase;
            padding: 8px 5px 8px 30px;
            font-size: 30px;
            background: #F4846F;
            color: white;
        }

        #invoice-title-number #number {
            margin-left: 10px;
            padding: 8px 0;
            font-size: 30px;
        }

        #client-info {
            float: left;
            margin-left: 30px;
            min-width: 220px;
            line-height: 18px;
        }

        #client-info>div {
            margin-bottom: 3px;
            min-width: 20px;
        }

        #client-info span {
            display: block;
            min-width: 20px;
        }

        #client-info>span {
            text-transform: uppercase;
        }

        table {
            table-layout: fixed;
        }

        table th,
        table td {
            vertical-align: top;
            word-break: keep-all;
            word-wrap: break-word;
        }

        #items {
            margin: 25px 30px 0 30px;
        }

        #items .first-cell,
        #items table th:first-child,
        #items table td:first-child {
            width: 40px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            text-align: center;
        }

        #items table {
            border-collapse: separate;
            width: 100%;
        }

        #items table th {
            font-weight: bold;
            padding: 5px 8px;
            text-align: right;
            background: #B32C39;
            color: white;
            text-transform: uppercase;
        }

        #items table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #items table th:last-child {
            text-align: right;
        }

        #items table td {
            padding: 9px 8px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }

        #items table td:nth-child(2) {
            text-align: left;
        }

        #sums table {
            width: 50%;
            float: right;
            margin-right: 30px;
        }

        #sums table tr th,
        #sums table tr td {
            min-width: 100px;
            padding: 9px 8px;
            text-align: right;
        }

        #sums table tr th {
            width: 70%;
            font-weight: bold;
            padding-right: 35px;
        }

        #sums table tr td.last {
            min-width: 0 !important;
            max-width: 0 !important;
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        #sums table tr.amount-total th {
            text-transform: uppercase;
        }

        #sums table tr.amount-total th,
        #sums table tr.amount-total td {
            font-size: 15px;
            font-weight: bold;
        }

        #invoice-info {
            margin: 10px 30px;
            line-height: 18px;
        }

        #invoice-info>div>span {
            display: inline-block;
            min-width: 20px;
            min-height: 18px;
            margin-bottom: 3px;
        }

        #invoice-info>div>span:first-child {
            color: black;
        }

        #invoice-info>div>span:last-child {
            color: #aaa;
        }

        #invoice-info:after {
            content: '';
            display: block;
            clear: both;
        }

        #terms .notes {
            min-height: 30px;
            min-width: 50px;
            margin: 0 30px;
        }

        #calculate_tax .calculate_tax {
            min-height: 30px;
            min-width: 50px;
            margin: 10px 0 0 30px;
        }

        #terms .payment-info div {
            margin-bottom: 3px;
            min-width: 20px;
        }

        .thank-you {
            margin: 10px 0 30px 0;
            display: inline-block;
            min-width: 20px;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 0.88em;
            float: right;
            padding: 5px 30px 0 2px;
            font-size: 20px;
            background: #F4846F;
            color: white;
        }

        .ib_bottom_row_commands {
            margin-left: 30px !important;
        }

        .item-summary {
            font-size: 12px
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .text-white {
            color: white;
        }

        /**
         * If the printed invoice is not looking as expected you may tune up
         * the print styles (you can use !important to override styles)
         */
        @media print {
            /* Here goes your print styles */
        }

        .page_break {
            page-break-before: always;
        }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }

        table td.text-center {
            text-align: center;
        }

        #itemsPayment,
        .box-title {
            margin: 25px 30px 0 30px;
        }

        #itemsPayment .first-cell,
        #itemsPayment table th:first-child,
        #itemsPayment table td:first-child {
            width: 40px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            text-align: center;
        }

        #itemsPayment table {
            border-collapse: separate;
            width: 100%;
        }

        #itemsPayment table th {
            font-weight: bold;
            padding: 5px 8px;
            text-align: right;
            background: #B32C39;
            color: white;
            text-transform: uppercase;
        }

        #itemsPayment table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #itemsPayment table th:last-child {
            text-align: right;
        }

        #itemsPayment table td {
            padding: 9px 8px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }

        #itemsPayment table td:nth-child(2) {
            text-align: left;
        }

        table th,
        table td {
            vertical-align: top;
            word-break: keep-all;
            word-wrap: break-word;
        }

        .word-break {
            word-wrap: break-word;
        }

        #notes {
            color: #767676;
            font-size: 11px;
            margin-top: 10px;
            margin-left: 40px;
        }

        #field-title {
            font-size: 13px;
            margin-top: 10px;
            margin-left: 40px;
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
            float: right !important;
            height: 80px
        }

        .client-logo-div{
            position: absolute;
            right: 0;
            margin-top: -150px;
            margin-right: 20px
        }


    </style>
</head>

<body>
    <div id="container">
        <section id="memo" class="description">
            <div class="logo">
                <img src="{{ $globalInvoiceSetting->logo_url }}" />
            </div>

            <div class="company-info">
                <div class="description">
                    {{ $globalInvoiceSetting->billing_name }}
                </div>
                <br />
                <span>{{ $superadmin->company_phone }}</span>
                <br />
                <span>{!! nl2br($globalInvoiceSetting->billing_address) !!}</span>
                <br />
                @if(!is_null($globalInvoiceSetting->billing_tax_name))
                    <span>{{ $globalInvoiceSetting->billing_tax_name }}: {{ $globalInvoiceSetting->billing_tax_id }}</span>
                    <br>
                @endif
            </div>
        </section>

        <section id="invoice-title-number" class="description">
            <span id="title">#</span>
            <span id="number">{{ $invoice->invoice_number }}</span>
        </section>


        <div class="clearfix"></div>
            <section id="client-info" class="description">
                <span>@lang('modules.invoices.billedTo'):</span>
                    <div>
                        <span class="bold">{{ $company->company_name }}</span>
                    </div>
                    <div class="mb-3">
                        <b>@lang('app.address') :</b>
                        <div>{!! nl2br($company->address) !!}</div>
                    </div>
            </section>
        <div class="clearfix"></div>

        <br>

        <section id="items">

            <table cellpadding="0" cellspacing="0">

                <tr>
                    <th>#</th>
                    <th class="description">@lang("app.description")</th>
                    <th class="description">@lang("app.date")</th>
                    <th class="description">@lang("app.amount") ({!! htmlentities($invoice->currency->currency_code) !!})</th>
                </tr>

                <tr data-iterate="item">
                    <td>1</td>
                    <!-- Don't remove this column as it's needed for the row commands -->
                    <td>
                        {{ $invoice->package->name  }} @if($invoice->package->default != 'trial' && $invoice->package->default != 'lifetime') - @lang('superadmin.'.$invoice->company->package_type )@endif
                    </td>
                    <td>{{ $invoice->pay_date?->format($global->date_format) }} @if($invoice->next_pay_date) - {{ $invoice->next_pay_date->format($global->date_format) }} @endif</td>
                    <td>@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
            </table>

        </section>

        <section id="sums" class="description">

            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th>@lang('modules.invoices.subTotal'):</th>
                    <td>@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
                <tr class="amount-total">
                    <th>@lang('modules.invoices.total'):</th>
                    <td>@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                </tr>
                @if ($globalInvoiceSetting->authorised_signatory && $globalInvoiceSetting->authorised_signatory_signature)
                    <tr>
                        <td colspan="2" style="font-size:15px" align="right">
                            <img style="height:95px; margin-bottom: -50px; margin-top: 5px;"
                            src="{{ $globalInvoiceSetting->authorised_signatory_signature_url }}" alt="{{ $company->company_name }}"/><br><br>
                            <p style="margin-top: 25px;">@lang('modules.invoiceSettings.authorisedSignatory')</p>
                        </td>
                    </tr>
                @endif
            </table>

            <div class="clearfix"></div>

        </section>
            <section id="invoice-info" class="description">
                <div class="description">
                    <span>@lang('modules.invoices.invoiceDate'):</span>
                    <span>{{ !is_null($invoice->pay_date) ? $invoice->pay_date->translatedFormat($company->date_format) : $invoice->created_at->translatedFormat($company->date_format) }}</span>
                </div>

            </section>
        <section id="terms">
            <div class="notes word-break description">
                <br>@lang('modules.invoiceSettings.invoiceTerms') <br>{!! nl2br($globalInvoiceSetting->invoice_terms) !!}
            </div>
        </section>


    </div>
</body>

</html>

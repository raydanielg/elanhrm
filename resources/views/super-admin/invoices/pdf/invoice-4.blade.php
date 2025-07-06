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
        font-family: {{$font}}, Arial, Helvetica, sans-serif;
    }

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
            font: inherit;
            font-size: 12px;
            vertical-align: baseline;
            /* font-family: Verdana, Arial, Helvetica, sans-serif; */
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

        .x-hidden {
            display: none !important;
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
            margin: 0 auto;
            position: relative;
        }

        .right-invoice {
            padding: 40px 30px;
        }

        #memo .company-info {
            float: left;
        }

        #memo .company-info div {
            font-size: 12px;
            text-transform: uppercase;
            min-width: 20px;
            line-height: 1em;
        }

        #memo .company-info span {
            font-size: 12px;
            display: inline-block;
            min-width: 20px;
        }

        #memo .logo {
            float: right;
            margin-left: 15px;
        }

        #memo .logo img {
            height: 50px;
        }

        #memo:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number {
            margin: 50px 0 20px 0;
            display: inline-block;
            float: left;
        }

        #invoice-title-number .title-top {
            font-size: 15px;
            margin-bottom: 5px;
        }

        #invoice-title-number .title-top span {
            display: inline-block;
            min-width: 20px;
        }

        #invoice-title-number .title-top #number {
            text-align: right;
            float: right;
            color: #858585;
        }

        #invoice-title-number .title-top:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number #title {
            display: inline-block;
            background: #415472;
            color: white;
            font-size: 25px !important;
            padding: 8px 13px;
        }

        #client-info {
            text-align: right;
            min-width: 220px;
            line-height: 21px;
            font-size: 12px;
        }

        .client-name {
            font-weight: bold !important;
            font-size: 15px !important;
            text-transform: uppercase;
            margin: 7px 0;
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
            color: #858585;
            font-size: 15px;
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

        #invoice-info {
            float: left;
            margin-top: 10px;
            line-height: 18px;
        }

        #invoice-info div {
            margin-bottom: 3px;
        }

        #invoice-info div span {
            display: inline-block;
            min-width: 20px;
            min-height: 18px;
        }

        #invoice-info div span:first-child {
            font-weight: bold;
            margin-right: 10px;
        }

        .currency {
            margin-top: 20px;
            text-align: right;
            color: #858585;
            font-style: italic;
            font-size: 12px;
        }

        .currency span {
            display: inline-block;
            min-width: 20px;
        }

        #items {
            margin-top: 10px;
        }

        #items .first-cell,
        #items table th:first-child,
        #items table td:first-child {
            width: 18px;
            text-align: left;
        }

        #items table {
            border-collapse: separate;
            width: 100%;
        }

        #items table th {
            font-size: 12px;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }

        #items table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #items table th:last-child {
            /*text-align: right;*/
        }

        #items table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        #items table td:first-child {
            text-align: left;
        }

        #items table td:nth-child(2) {
            text-align: left;
        }

        #sums {
            margin: 25px 30px 0 0;
            width: 100%;
        }

        #sums table {
            width: 70%;
            float: right;
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
        }

        #sums table tr td.last {
            min-width: 0 !important;
            max-width: 0 !important;
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        #sums table tr.amount-total td,
        #sums table tr.amount-total th {
            font-size: 20px !important;
        }

        #sums table tr.due-amount th,
        #sums table tr.due-amount td {
            font-weight: bold;
        }

        #sums:after {
            content: '';
            display: block;
            clear: both;
        }

        #terms {
            margin-top: 20px !important;
            font-size: 12px;
        }

        .calculate_tax {
            margin-top: 20px !important;
            font-size: 12px;
        }

        #terms>span {
            font-weight: bold;
            display: inline-block;
            min-width: 20px;
        }

        #terms>div {
            min-height: 50px;
            min-width: 50px;
        }

        #terms .notes {
            min-height: 30px;
            min-width: 50px;
        }

        .item-summary {
            font-size: 11px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 1rem;
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

        #itemsPayment {
            margin-top: 10px;
        }

        #itemsPayment .first-cell,
        #itemsPayment table th:first-child,
        #itemsPayment table td:first-child {
            width: 18px;
            text-align: right;
        }

        #itemsPayment table {
            border-collapse: separate;
            width: 100%;
        }

        #itemsPayment table th {
            font-size: 12px;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }

        #itemsPayment table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #itemsPayment table th:last-child {
            /*text-align: right;*/
        }

        #itemsPayment table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        #itemsPayment table td:first-child {
            text-align: left;
        }

        #itemsPayment table td:nth-child(2) {
            text-align: left;
        }

        #itemsPayment,
        .box-title {
            margin: 25px 30px 0 30px;
        }

        .word-break {
            word-wrap: break-word;
        }

        .left-stripes {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100px;
            background: url("{{ asset("img/stripe-bg.jpg") }}") repeat;
        }
        .left-stripes .circle {
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            background: #415472;
            width: 30px;
            height: 30px;
            position: absolute;
            left: 33%;
        }
        .left-stripes .circle.c-upper {
            top: 440px;
        }
        .left-stripes .circle.c-lower {
            top: 690px;
        }

        #notes {
            color: #767676;
            font-size: 11px;
            margin-top: 10px;
            margin-left: 40px;
        }

        #field-title {
            color: #504f4f;
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

            #memo .client-logo {
            float:left;
            display: flex !important;
            flex-direction: column !important;
            position: absolute;
            /* margin-bottom: 15px; */
            }

            #memo .client-logo img {
                height: 50px;
                margin-bottom: 10px;
            }

        @endif

    </style>

</head>

<body>
    <div id="container">

        <div class="right-invoice">
            <section id="memo">
                <div class="client-logo">
                    <div>
                        <img style="height:100px;" src="{{ $globalInvoiceSetting->logo_url }}" alt="{{$globalInvoiceSetting->billing_name}}" />
                    </div>
                    <div class="company-info description">
                            <br>
                        <div class="description">
                            {{ $globalInvoiceSetting->billing_name }}
                        </div>

                        @if($superadmin->company_phone) <span class="description">{{ $superadmin->company_phone }}</span><br>@endif

                        <span class="description">{!! nl2br($globalInvoiceSetting->billing_address) !!}</span>
                        @if(!is_null($globalInvoiceSetting->billing_tax_name))
                            <span class="description">{{ $globalInvoiceSetting->billing_tax_name }}: {{ $globalInvoiceSetting->billing_tax_id }}</span>
                            <br>
                        @endif
                    </div>
                </div>

                <div class="logo">
                        <img src="{{ $company->logo_url }}"
                            alt="{{ $company->company_name }}" class="logo"/>

                </div>
            </section>

            <section id="invoice-title-number">
                    <div class="title-top description" >
                        <span>@lang('modules.invoices.invoiceDate'):</span>
                        <span>{{ !is_null($invoice->pay_date) ? $invoice->pay_date->translatedFormat($company->date_format) :  $invoice->created_at->translatedFormat($company->date_format)}}</span>
                    </div>
                <div id="title">{{ $invoice->invoice_number }}</div>
            </section>
            <section id="client-info">
                    <span>@lang('modules.invoices.billedTo'):</span>
                    <div class="client-name">
                        <strong>{{ $company->company_name }}</strong>
                    </div>
                    <div class="mb-3">
                        <span>
                            <b>@lang('app.address') :</b><br>
                            {!! nl2br($company->address) !!}
                        </span>
                    </div>

                </section>


            <div class="clearfix"></div>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <section id="items">

                <table cellpadding="0" cellspacing="0" >

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
                            {{ $invoice->package->name  }}    {{ $invoice->package->default != 'trial' ? '- '.  __('superadmin.'.$invoice->company->package_type) : ''}}
                        </td>
                        <td>{{ $invoice->pay_date?->format($global->date_format)}} @if($invoice->next_pay_date) - {{ $invoice->next_pay_date->format($global->date_format) }} @endif</td>
                        <td>@if(!is_null($invoice->currency)){!! htmlentities($invoice->currency->currency_code)  !!}@else ₹ @endif{{ number_format((float)$invoice->total, 2, '.', '') }}</td>
                    </tr>

                </table>

            </section>

            <section id="sums">

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
                            src="{{ $globalInvoiceSetting->authorised_signatory_signature_url }}" alt="{{ $global->company_name }}"/><br><br>
                            <p style="margin-top: 35px;">@lang('modules.invoiceSettings.authorisedSignatory')</p>
                        </td>
                    </tr>
                    @endif
                </table>

            </section>


            <div class="clearfix"></div>

            <section id="terms">

                <div class="notes word-break description">
                        <br><br>@lang('modules.invoiceSettings.invoiceTerms') <br>{!! nl2br($globalInvoiceSetting->invoice_terms) !!}
                </div>

            </section>
        </div>
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        .container {
            width: 100%;
            max-width: 1140px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .bill-to,  .items-table, .signature {
            margin-top: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 5px solid #ffc107;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header img {
            width: 120px;
        }
        .header h3 {
            color: #EB7D1E;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 0;
        }

        .bill-to p, .totals p {
            margin: 5px 0;
        }
        
        table{width: 100%;border-collapse: collapse;}       
        .invoice-info table{ border-bottom: 3px solid #dee2e6;text-align: left;}
        .invoice-info td:nth-child(2){text-align: right;}

        .items-table table { margin-top: 20px;}
        .items-table th, .items-table td{
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .items-table th:nth-child(3),.items-table td:nth-child(3){
            text-align: center;
        }

        .items-table td  p {
            margin: 0;
        }
        
        .totals table{ width: 100%;border-collapse: collapse;}
        .totals td:nth-child(1){text-align: left;}
        .totals td:nth-child(2){text-align: right;}

        .signature{
            text-align: right;
        }
        .signature img {
            width: 150px;
        }
        .signature p {
            margin: 0;
        }
        .text-end{text-align: right;}
        .fw-normal {font-weight: 400 !important;}
        .fs-1 {font-size: .625rem !important;}
        .fs-2 {font-size: .75rem !important;}
        .pb-1 {padding-bottom: .25rem !important;}
        .p-1 {padding: .25rem !important;}
        .mb-0 {margin-bottom: 0 !important;}
        .mt-0 {margin-top: 0 !important;}
        .mt-1 {margin-top: .25rem !important;}
        .px-4 {padding-right: 1.5rem !important;padding-left: 1.5rem !important;}
        .dejaVu {font-family: 'DejaVu Sans', sans-serif;}

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div id="border-less">
                <table>
                    <thead>
                        <tr class="text-dark fw-bold">
                            <td><img src="data:image/png;base64,{{ $logoData }}" alt="Company Logo"></td>
                            <td>
                                <h3><span class="irnSymbol"></span>Smart Construction And Interiors</h3>
                                <p>Aarthi theatre road, Dindigul, Tamil Nadu, 624001</p>
                                <p><b>Mobile:</b> 8825979705</p>
                                <p><b>Email:</b> smartinteriors2020@gmail.com</p>
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="invoice-info">
            <table>
                <thead>
                    <tr>
                        <td><p class="p-1 mb-0 mt-1"><b>Date :</b> {{ $created_date }}</p></td>
                        <td><p class="pb-0 mb-0 mt-0">{{ $invoice_number }}</p></td>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="bill-to">
            <p class="fs-3 pb-0 mb-0 fw-semibold"><b>CREATED BY</b></p>
            <p class="px-4">{{ $createdby_name }},</p>
        </div>
       
        <div class="items-table">
            <table>
                <thead>
                    <tr class="text-dark fw-bold">
                        <th>DESIGN</th>
                        <th class="text-end">DESCRIPTION</th>
                        <th class="text-center">DIMENSION</th>
                        <th class="text-center">QUANTITY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderItems as $item)
                    <tr>
                        <td class="fw-semibold">
                            @if ($item['imageData'])
                                <img src="data:image/png;base64,{{ $item['imageData'] }}" alt="" style="max-width: 100px;">
                            @endif
                        </td>
                        <td class="fw-semibold">
                        <b>{{ $item['category_name']  }}</b>
                            <p class="fs-2 pb-1 mb-0 fw-normal">{{ $item['design_name'] }}</p>
                        </td>
                        <td class="text-center">
                            <p class="fs-3 pb-0 mb-0">{{ $item['dimension'] != null ? $item['dimension'] : 'N/A' }}</p>
                        </td>
                        <td class="text-center">
                            <p class="fs-3 pb-0 mb-0"><span class="dejaVu">{{ $item['quantity'] }}</span>({{ $item['unit'] ? $item['unit'] : "SQ.FT" }})</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="signature">
            <img src="data:image/png;base64,{{ $signatureData }}" alt="">
            <p class="fw-bold mt-3 mb-0 text-dark"><b>AUTHORISED SIGNATORY FOR</b></p>
            <p>Smart Construction And Interiors</p>
        </div>
    </div>
</body>
</html>

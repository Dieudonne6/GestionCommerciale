@extends('layouts.master')

@section('content')
<div class="print-actions">
    <button onclick="window.print()">Imprimer</button>
</div>

<div class="invoice-container">
    
    <!-- Entête -->
    <div class="invoice-header">
        <div class="left">
            <strong> {{ $nomEntreprise }} </strong>
        </div>
        <div class="right">
           {{ $reffacture }}
        </div>
    </div>
    
    <!-- infos client -->
    <div class="client-info">
        <div>{{ $IFUEntreprise }}</div>
        <div>{{ $adresseEntreprise }}</div>
        <div> {{ $telEntreprise }} </div>
        <div> {{ $mailEntreprise }} </div>
    </div>
    <hr>

    <!-- info vendeur -->
    <div class="vendor-info">
        <div><span class="label">Vendeur:</span> <span class="value"> {{ $nomEntreprise }}</span></div>
        <div><span class="label">IFU de l'acheteur:</span> <span class="value"> {{ $IFUClient }} </span></div>
        <div><span class="label">Nom de l'acheteur:</span> <span class="value">{{ $nomcompletClient }}</span></div>
        <div><span class="label">Tél de l'acheteur:</span> <span class="value">{{ $telClient }}</span></div>
    </div>
    <hr>

    <div class="separator"><strong>--- FACTURE ---</strong></div>
    <hr> 

    {{-- @dd($itemFacture) --}}

    {{-- @dump($facturedetaille) --}}
    <!-- infos achat    -->

    @if ($regime == 'TVA')
        <div class="items">
            @foreach ($itemFacture as $item)
                <div class="item">
                    <div class="description"> {{ $item['quantity']}}  x {{number_format( $item['price'] , 0, ',', '.')}}<br> {{ $item['name'] }} (B)</div>
                    <div class="amount"> {{number_format( ($item['price']  * $item['quantity']), 0, ',', '.') }} </div>
                </div>
            @endforeach
        </div>
        <hr>
        <div class="totals">
            <div><span class="label">Total</span> <span class="value"> {{number_format( $montanttotal , 0, ',', '.')}} </span></div>
            <div><span class="label">Total H.T. [B] (18 %)</span> <span class="value"> {{number_format( ($montanttotal - $TotalTVA - $montantaib), 0, ',', '.')  }} </span></div>
            <div><span class="label">Total TVA [B](18 %)</span> <span class="value"> {{ number_format($TotalTVA , 0, ',', '.')}} </span></div>
            @if ($montantaib > 0)
                <div><span class="label" style="font-weight: bold">AIB 1%</span> <span class="value"> {{ number_format($montantaib , 0, ',', '.')}} </span></div>                
            @endif
            <div><span class="label"> {{ $libellModepaie }}</span> <span class="value">{{ number_format($montanttotal, 0, ',', '.') }}</span></div>
        </div>
    @else
        <div class="items">
            @foreach ($itemFacture as $item)
                <div class="item">
                    <div class="description"> {{ $item['quantity']}}  x {{number_format( $item['price'], 0, ',', '.') }}<br> {{ $item['name'] }} (B)</div>
                    <div class="amount">{{ number_format(($item['price']  * $item['quantity']), 0, ',', '.') }} </div>
                </div>
            @endforeach
        </div>
        <hr>
        <div class="totals">
            <div><span class="label">Total</span> <span class="value"> {{ number_format($montanttotal , 0, ',', '.')}} </span></div>
            <div><span class="label">REGIME TPS [E]</span> <span class="value"> {{ number_format($montanttotal , 0, ',', '.')}} </span></div>
            @if ($montantaib > 0)
                <div><span class="label" style="font-weight: bold">AIB 1%</span> <span class="value"> {{ number_format($montantaib , 0, ',', '.')}} </span></div>                
            @endif
            <div><span class="label"> {{ $libellModepaie }} </span> <span class="value">{{ number_format($montanttotal, 0, ',', '.') }}</span></div>
        </div>
    @endif



    <hr>
    <!-- Codes et QR -->
    <div style="text-align: center;"><strong>Code MECeF/DGI</strong></div>
    <div style="text-align: center;"> {{ $factureconfirm['codeMECeFDGI'] }} </div>
    <div class="vendor-info">        
        <div><span class="label">MECeF NIN:</span> <span class="value">{{ $factureconfirm['nim'] }}</span> </div>
        <div> <span class="label"> MECeF Compteurs: </span> <span class="value"> {{ $factureconfirm['counters'] }} </span></div>
        <div> <span class="label">MECeF Heure:</span>  <span class="value"> {{ $factureconfirm['dateTime'] }} </span></div>
         <div class="qr"><img src="data:image/jpeg;base64,{{ base64_encode($qrcodecontent) }}" alt="QR Code"></div>
    </div>
</div>

<style>

   /* FORMAT ÉCRAN */
    .invoice-container {
        font-family: monospace;
        font-size: 12px;
        line-height: 1.3;
        width: 80mm;             
        margin: auto;
        padding: 6mm;
    }

    /* IMPRESSION TICKET */
    @media print {

        @page {
            size: 80mm auto;      
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 58mm;
            padding: 4mm;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        .print-actions {
            display: none;
        }
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .client-info{
        text-align: center;
    }
    .vendor-info div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px; 
    }
    .label {
        font-weight: bold;
    }
    .value {
        text-align: right;
    }

    .separator {
        text-align: center;
        margin: 10px 0;
    }

    .items .item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .items .description {
        max-width: 70%;
    }
    .items .amount {
        text-align: right;
    }

    .totals div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .totals .label {
        font-weight: bold;
    }
    .totals .value {
        text-align: right;
    }
    
    @media print {
        body {
            margin: 0;
        }
        .invoice-container {
            padding: 0;
        }
    }

    .print-actions {
        text-align: center;
        margin-bottom: 15px;
    }

    .print-actions button {
        padding: 6px 14px;
        font-size: 14px;
        cursor: pointer;
    }

    @media print {
        .print-actions {
            display: none; 
        }
    }

    .qr {
        display: flex !important;
        justify-content: center !important;
        align-items: center;
        text-align: center;
       
    }

</style>
@endsection

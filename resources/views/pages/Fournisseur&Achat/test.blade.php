@extends('layouts.master')

@section('content')
<div class="print-actions">
    <button onclick="window.print()">Imprimer</button>
</div>

<div class="invoice-container">
    
    <!-- Entête -->
    <div class="invoice-header">
        <div class="left">
            <strong>CRYSTAL </strong>
        </div>
        <div class="right">
           Date Heure
        </div>
    </div>
    
    <!-- infos client -->
    <div class="client-info">
        <div>Porto-Novo</div>
        <div>00000000</div>
        <div>facture@gmail.com</div>
    </div>
    <hr>

    <!-- info vendeur -->
    <div class="vendor-info">
        <div><span class="label">Vendeur:</span> <span class="value">CRYSTAL</span></div>
        <div><span class="label">ID de l'acheteur:</span> <span class="value">12345</span></div>
        <div><span class="label">Nom de l'acheteur:</span> <span class="value">CCCC</span></div>
        <div><span class="label">Tél de l'acheteur:</span> <span class="value">0155555555</span></div>
    </div>
    <hr>

    <div class="separator">--- FACTURE ---</div>
    <hr> 

    <!-- infos achat    -->
    <div class="items">
        <div class="item">
            <div class="description">9 x 75.000<br> Téléphones Android (9)</div>
            <div class="amount">100000</div>
        </div>
        <div class="item">
            <div class="description">4 x 800<br> Trépied (4)</div>
            <div class="amount">3.200</div>
        </div>
    </div>
    <hr>
    <div class="totals">
        <div><span class="label">Total:</span> <span class="value">100000</span></div>
        <div><span class="label">Total TVA (18 %):</span> <span class="value">100.076</span></div>
        <div><span class="label">Total TTC (18 %):</span> <span class="value">800000</span></div>
        <div><span class="label">EXPRESS:</span> <span class="value">100000</span></div>
    </div>

    <hr>
    <!-- Codes et QR -->
    <div style="text-align: center;"><strong>Code MECeF/DGI</strong></div>
    <div style="text-align: center;">TEST-XXXX-XXXX-XXXX-H800-VJS2</div>
    <div class="vendor-info">        
        <div><span class="label">MECeF NIN:</span> <span class="value">7501012125</span> </div>
        <div> <span class="label"> MECeF Compteurs: </span> <span class="value">7501012125</span></div>
        <div> <span class="label">MECeF Heure:</span>  <span class="value">7501012125</span></div>
         <div class="qr"><img src="{{  'https://via.placeholder.com/100' }}" alt="QR Code"></div>
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

   
    .vendor-info .qr {
        display: flex !important;
        justify-content: center !important;
        width: 100%;
        margin-top: 10px;
    }

</style>
@endsection

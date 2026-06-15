<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.bahan']);
        
        $pdf = Pdf::loadView('pdf.purchase-order', ['po' => $purchaseOrder]);
        
        $fileName = "PO-" . str_replace(['/', '\\'], '-', $purchaseOrder->no_po) . ".pdf";
        
        return $pdf->stream($fileName);
    }
}

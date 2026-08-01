<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Support\CurrencyRate;

class CurrencyRateController extends Controller
{
    public function aedToEgp()
    {
        $meta = CurrencyRate::aedToEgpMeta();

        if (! $meta['ok']) {
            return response()->json([
                'ok' => false,
                'message' => 'تعذر جلب سعر الصرف حالياً',
            ], 503);
        }

        return response()->json($meta);
    }
}

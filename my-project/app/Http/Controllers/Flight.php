<?php

namespace App\Http\Controllers;

use App\Service\Flight as FlightService;
use Illuminate\Http\Request;

class Flight extends Controller
{
    /**
     * 搜索最短航班路径
     */
    public function search(Request $request) {
        $from = $request->get('from', '');
        $to = $request->get('to', '');
        if (empty($from) || empty($to)) {
            return response()->json([]);
        }
        $flightService = new FlightService();
        $path = $flightService->search($from, $to);
        return response()->json($path);
    }
}
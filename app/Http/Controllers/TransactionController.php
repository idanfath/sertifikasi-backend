<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $r)
    {
        $item = Transaction::query();
        if ($r->search) {
            $item = Transaction::where('column', 'like', '%' . $r->search . '%');
        }
        $result = $item->paginate($r->limit ?? 10);
        return response()->json([
            'data' => $result
        ], 200);
    }
    public function show($id) {}
    public function store(Request $r) {}
    public function update(Request $r, $id) {}
    public function destroyMany(Request $r) {}
}

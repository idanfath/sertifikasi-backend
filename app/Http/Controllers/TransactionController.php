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
            $item->where('column', 'like', '%' . $r->search . '%');
        }

        $result = $item->paginate($r->limit ?? 10);
        return response()->json([
            'data' => $result
        ], 200);
    }
    public function show($id)
    {
        return response()->json([
            'data' => Transaction::find($id)
        ], 200);
    }
    public function store(Request $r)
    {
        $v = $r->validate([
            'invoice' => 'required|string|min:8',
            'total' => 'required|min:1|numeric',
            'items' => 'required',
            'items.*.sku' => 'required|exists:items,sku',
            'items.*.id' => 'required|exists:items,id',
            'items.*.price' => 'required|numeric',
        ]);

        return response()->json([
            'message' => 'success',
            'data' => Transaction::create($v),
        ], 200);
    }
    public function update(Request $r, $id)
    {
        $item = Transaction::find($id);
        $item->update($r->all());

        return response()->json([
            'message' => 'success',
            'data' => $item,
        ], 200);
    }
    public function destroyMany(Request $r)
    {
        $v = $r->validate([
            'id' => 'required',
            'id.*' => 'required|exists:items,id'
        ]);

        foreach ($v['id'] as $item) {
            Transaction::find($item)->delete();
        }

        return response()->json([
            'message' => 'success deleting'
        ], 200);
    }
}

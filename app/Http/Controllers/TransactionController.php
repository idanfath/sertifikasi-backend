<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $r)
    {
        $item = Transaction::query();

        $item->with('user');

        if ($r->search) {
            $item->where(function ($query) use ($r) {
                $query->where('invoice', 'ilike', '%' . $r->search . '%')
                    ->orWhereRaw("items::jsonb @> ?", [
                        json_encode([['name' => $r->search]])
                    ])
                    ->orWhereRaw("items::jsonb @> ?", [
                        json_encode([['sku' => $r->search]])
                    ]);
            });
        }

        $item->orderBy('created_at', 'desc');

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
            'paid_amount' => 'required|numeric',
            'items' => 'required',
            'items.*.sku' => 'required|exists:items,sku',
            'items.*.id' => 'required|exists:items,id',
            'items.*.name' => 'required|string',
            'items.*.amount' => 'required|numeric',
        ]);

        $total = 0;
        foreach ($v['items'] as $i) {
            $itemModel = Item::find($i['id']);

            $i['price'] = $itemModel->price;
            $total += $i['price'] * $i['amount'];

            if ($itemModel->stock < $i['amount']) {
                return response()->json([
                    'message' => 'stock not enough',
                ], 400);
            }

            $itemModel->stock -= $i['amount'];
            $itemModel->save();

            $i['subtotal'] = $i['price'] * $i['amount'];
        }

        $v['total'] = $total;
        $v['invoice'] = md5(time() . $r->user()->id . $r->user()->name);

        if ($v['paid_amount'] < $v['total']) {
            return response()->json([
                'message' => 'paid amount not enough',
            ], 400);
        }

        $v['change'] = floor(($v['paid_amount'] - $v['total']) / 100) * 100;
        $v['items'] = $v['items'];
        $v['user_id'] = $r->user()->id;

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
            'id.*' => 'required|exists:transactions,id'
        ]);

        Transaction::destroy($v['id']);

        return response()->json([
            'message' => 'success deleting'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $r)
    {
        $item = Item::query();
        if ($r->search) {
            $item->where('name', 'like', '%' . $r->search . '%');
        }
        $result = $item->paginate($r->limit ?? 10);
        return response()->json([
            'data' => $result
        ], 200);
    }
    public function show($id)
    {
        return response()->json([
            'data' => Item::find($id)
        ], 200);
    }
    public function store(Request $r)
    {
        $v = $r->validate([
            'name' => 'required',
            'stock' => 'required|min:1|numeric',
            'price' => 'required|float',
            'sku' => 'required'
        ]);

        return response()->json([
            'message' => 'success',
            'data' => Item::create($v),
        ], 200);
    }
    public function update(Request $r, $id)
    {
        $item = Item::find($id);
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
            Item::find($item)->delete();
        }

        return response()->json([
            'message' => 'success deleting'
        ], 200);
    }
}

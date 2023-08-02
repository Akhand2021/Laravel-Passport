<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();
        return response(['data' => $items,], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer',
        ]);

        $item = Item::create($request->all());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
 
     public function update(Request $request, Item $item)
     {
         $request->validate([
             'name' => 'sometimes|required|string|max:255',
             'description' => 'nullable|string',
             'price' => 'sometimes|required|integer',
         ]);
 
         $item->update($request->all());
         return response()->json($item);
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(null, 204);
    }  
}

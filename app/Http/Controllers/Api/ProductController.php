<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit; 

class ProductController extends Controller
{
    //
    public function incrementView($id) {
        $product = Produit::findOrFail($id);
        $product->increment('visites');
        return response()->json(['success' => true]);
    }
}

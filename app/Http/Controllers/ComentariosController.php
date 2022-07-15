<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentarios;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class ComentariosController extends Controller
{
    public function reviews(Request $request)
    {
        $reviews = Comentarios::select('ecommerce_comentarios.ecommerce_comentariosid', 'ecommerce_comentarios.productosid', 'ecommerce_comentarios.clientesid', 'ecommerce_comentarios.valoracion', 'ecommerce_comentarios.comentario', 'ecommerce_comentarios.estado', 'productos.descripcion', 'clientes.razonsocial', 'clientes.email')
            ->join('productos', 'productos.productosid', '=', 'ecommerce_comentarios.productosid')
            ->join('clientes', 'clientes.clientesid', '=', 'ecommerce_comentarios.clientesid');

        if ($request->rating) {
            $reviews = $reviews->orderBy('ecommerce_comentarios.valoracion', $request->rating)->paginate(15);
            return view('backend.reviews', compact('reviews'));
        } else {
            $reviews = $reviews->orderBy('ecommerce_comentarios.fechacreacion', 'desc')->paginate(15);
            return view('backend.reviews', compact('reviews'));
        }
    }

    public function actualizarPublicado(Request $request)
    {
        $review = Comentarios::findOrFail($request->ecommerce_comentariosid);
        $review->estado = $request->estado;
        if ($review->save()) {
            $product = Producto::findOrFail($review->productosid);
            $parametros = json_decode($product->parametros_json);
            if (count(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->get()) > 0) {
                $parametros->rating = number_format(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->sum('valoracion') / count(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->get()), 2);
            } else {
                $parametros->rating = number_format(0, 2);
            }
            $product->update(['parametros_json' => json_encode($parametros)]);
            return 1;
        }
        return 0;
    }

    public function store(Request $request)
    {
        $review = new Comentarios();
        $review->productosid = $request->productosid;
        $review->clientesid = Auth::user()->clientesid;
        $review->valoracion = $request->rating;
        $review->comentario = $request->comentario;
        $review->estado = '1';
        $review->fechacreacion = now();
        if ($review->save()) {
            $product = Producto::findOrFail($request->productosid);
            $parametros = json_decode($product->parametros_json);
            if (count(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->get()) > 0) {
                $parametros->rating = number_format(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->sum('valoracion') / count(Comentarios::where('productosid', $product->productosid)->where('estado', 1)->get()), 2);
            } else {
                $parametros->rating = number_format(0, 2);
            }
            $product->update(['parametros_json' => json_encode($parametros)]);
            flash('Comentario Publicado Exitosamente')->success();
            return back();
        }
        flash('Algo Salio Mal')->error();
        return back();
    }
}

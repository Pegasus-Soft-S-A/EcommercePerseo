@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <b class="h4">Lista de Deseos</b>
        </div>
    </div>
</div>

<div class="row gutters-5">
    @forelse ($wishlists as $key => $wishlist)
    @if ($wishlist->productosid != null)
    <div class="col-xxl-3 col-xl-4 col-lg-3 col-md-4 col-sm-6" id="wishlist_{{ $wishlist->ecommerce_lista_deseosid }}">
        <div class="card mb-2 shadow-sm">
            <div class="card-body">
                <a href="{{ route('product', $wishlist->productosid) }}" class="d-block mb-3">
                    <img src="data:image/jpg;base64,@if ($wishlist->imagen) {{ base64_encode($wishlist->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                        class="img-fit h-140px h-md-200px">
                </a>

                <h5 class="fs-14 mb-0 lh-1-5 fw-600 text-truncate">
                    <a href="{{ route('product', $wishlist->productosid) }}"
                        class="text-reset">@if(get_setting('ver_codigo')==1)
                        {{$wishlist->productocodigo}}-@endif{{ $wishlist->descripcion }}</a>
                </h5>
                <div class="rating rating-sm mb-1">
                    {{ renderStarRating(json_decode($wishlist->parametros_json)->rating) }}
                </div>
                <div class=" fs-14">
                    @if ($wishlist->precio<$wishlist->precio2)
                        <del class="fw-600 opacity-50 mr-1">${{ number_format(round($wishlist->precio2,2),2) }}</del>
                        @endif
                        <span class="fw-600 text-primary">${{ number_format(round($wishlist->precio,2),2) }}</span>
                </div>
            </div>
            <div class="card-footer">
                <a href="#" class="link link--style-3" data-toggle="tooltip" data-placement="top"
                    title="Eliminar de la lista de deseos"
                    onclick="removeFromWishlist({{ $wishlist->ecommerce_lista_deseosid }})">
                    <i class="la la-trash la-2x"></i>
                </a>
                <button type="button" class="btn btn-sm btn-block btn-primary ml-3"
                    onclick="showAddToCartModal({{ $wishlist->productosid }})">
                    <i class="la la-shopping-cart mr-2"></i>Agregar al Carrito
                </button>
            </div>
        </div>
    </div>
    @endif
    @empty
    <div class="col">
        <div class="text-center bg-white p-4 rounded shadow">
            <img class="mw-100 h-200px" src="{{ static_asset('assets/img/nothing.svg') }}" alt="Image">
            <h5 class="mb-0 h5 mt-3">No ha agregado nada todavia.</h5>
        </div>
    </div>
    @endforelse
</div>
<div class="aiz-pagination">
    {{$wishlists->appends(request()->input())->links('pagination::bootstrap-4')}}
</div>
@endsection

@section('modal')

<div class="modal fade" id="addToCart" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
        role="document">
        <div class="modal-content position-relative">
            <div class="c-preloader">
                <i class="fa fa-spin fa-spinner"></i>
            </div>
            <button type="button" class="close absolute-close-btn"  data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div id="addToCart-modal-body">

            </div>
        </div>
    </div>
</div>



@endsection
@section('script')
<script type="text/javascript">
    function removeFromWishlist(id){
            $.post('{{ route('wishlist.remove') }}',{_token:'{{ csrf_token() }}', id:id}, function(data){
                $('#wishlist').html(data);
                $('#wishlist_'+id).hide();
                AIZ.plugins.notify('success', 'El item ha sido removido de la lista de deseos');
            })
        }
</script>
@endsection
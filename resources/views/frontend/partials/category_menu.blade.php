<div class="aiz-category-menu bg-white rounded @if(Route::currentRouteName() == 'home') shadow-sm" @else shadow-lg"
    id="category-sidebar" @endif>
    <div class="p-3 bg-soft-primary d-none d-lg-block rounded-top all-category position-relative text-left">
        <span class="fw-600 fs-16 mr-3">{{ucfirst(get_setting('grupo_productos'))}}</span>
        <a href="@if (get_setting('vista_categorias')==1) {{ route('categories.all') }} @else {{ route('search') }} @endif"
            class="text-reset">
            <span class="d-none d-lg-inline-block">Ver todas</span>
        </a>
    </div>
    <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">
        @foreach (grupoProductos(11) as $key => $category)
        <li class="category-nav-element" data-id="1">
            <a href="{{ route('products.category', $category->id) }}"
                class="text-truncate text-reset py-2 px-3 d-block">
                <img class="cat-image lazyload mr-2 opacity-60"
                    src="data:image/jpg;base64,@if ($category->imagen) {{ base64_encode($category->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                    width="16" alt="{{ $category->descripcion }}">
                <span class="cat-name">{{ $category->descripcion }}</span>
            </a>


        </li>

        @endforeach


    </ul>

</div>
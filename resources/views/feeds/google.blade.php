<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>{{ config('app.name', 'Le Cameleon') }}</title>
        <link>{{ route('home') }}</link>
        <description>Catálogo de piezas vintage disponibles.</description>
        @foreach ($products as $product)
            @php
                $image = $product->images->first();
                $description = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($product->description ?: $product->short_description ?: $product->name))), 5000, '');
            @endphp
            <item>
                <g:id>{{ $product->id }}</g:id>
                <title>{{ $product->name }}</title>
                <description>{{ $description }}</description>
                <g:availability>in stock</g:availability>
                <g:condition>used</g:condition>
                <g:price>{{ number_format((float) $product->price, 2, '.', '') }} USD</g:price>
                <link>{{ route('shop.show', $product->slug) }}</link>
                <g:image_link>{{ \App\Support\PublicUrl::absolute($image?->url() ?? \App\Models\ProductImage::placeholderUrl()) }}</g:image_link>
                <g:brand>{{ $product->brand?->name ?: config('app.name', 'Le Cameleon') }}</g:brand>
                <g:item_group_id></g:item_group_id>
            </item>
        @endforeach
    </channel>
</rss>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ html_entity_decode($product->title) }}",
  "image": "{{ $product->image }}",
  "description": "{{ html_entity_decode($product->description) }}",
  "brand": {
    "@type": "Thing",
    "name": "{{ html_entity_decode($product->parentCategories()) }}"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "USD",
    "price": "{{ $product->price }}",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "Pursury.com"
    }
  }
}
</script>
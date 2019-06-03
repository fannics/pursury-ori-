<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "url": "{{ route('homepage', array(), true) }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ route('main_search', array('page' => null, 'term' => null), true) }}term={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

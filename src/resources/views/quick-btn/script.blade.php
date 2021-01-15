@push('scripts')
    <script>
    $('a.makeCompanyAndQuote').on('click', function(e){
        e.preventDefault();
        $('form#makeCompanyAndQuote-'+$(this).attr('data-id')).submit();
    });
    </script>
@endpush

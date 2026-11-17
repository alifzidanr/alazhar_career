@php
    /*
     * One "Terjadi Kendala Teknis" page for every error. The markup lives in
     * public/errors/kesalahan-teknis.html so nginx can serve the exact same page
     * for errors that never reach Laravel (413, 502, 504, ...). Here the nginx SSI
     * block that prints the status code is swapped for the code Laravel knows.
     */
    $statusCode = isset($exception) && method_exists($exception, 'getStatusCode')
        ? $exception->getStatusCode()
        : 500;

    $page = file_get_contents(public_path('errors/kesalahan-teknis.html'));

    $page = preg_replace(
        '/<!--# if expr=.*?<!--# endif -->/s',
        '<p class="code">Kode galat: '.e($statusCode).'</p>',
        $page
    );
@endphp
{!! $page !!}

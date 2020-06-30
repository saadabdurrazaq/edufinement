@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            Header Title
        @endcomponent
    @endslot

{{-- Body --}}
Hello!
<br><br>
You may have previously received an email from us, but we should inform you that it was a technical problem from our system.
<br><br>
Because of this, we havent announced you anything until you received a next email from us. Stay tuned, stay updates!
<br><br>
we are sorry for your inconvenience.
<br><br>
Regards,
<br>
Akademik 

{{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

{{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}.
        @endcomponent
    @endslot
@endcomponent
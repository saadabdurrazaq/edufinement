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
We appreciate that you took the time to apply for the position of student with our school. We received applications from many people. After reviewing your submitted application materials, we have decided that we will not offer you an interview.
                    <br><br>
We appricate that you are intersted in our school. Please do apply again in the future. Again, thank you for applying. We wish you all the best.

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
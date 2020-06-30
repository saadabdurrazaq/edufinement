@extends("layouts.app")

@section("title") Users list @endsection 

@section('index-admin-list')
{{ Breadcrumbs::render('list-active-admin') }}
@endsection

@section("content")
    
  @if(session('status'))
  <div class="alert alert-success">
    {{session('status')}}
  </div>
  @endif 

  {!! Menu::render() !!}
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
@push('custom-scripts')
<script>
	var menus = {
		"oneThemeLocationNoMenus" : "",
		"moveUp" : "Move up",
		"moveDown" : "Mover down",
		"moveToTop" : "Move top",
		"moveUnder" : "Move under of %s",
		"moveOutFrom" : "Out from under  %s",
		"under" : "Under %s",
		"outFrom" : "Out from %s",
		"menuFocus" : "%1$s. Element menu %2$d of %3$d.",
		"subMenuFocus" : "%1$s. Menu of subelement %2$d of %3$s."
	};
	var arraydata = [];     
	var addcustommenur= '{{ route("haddcustommenu") }}';
	var updateitemr= '{{ route("hupdateitem")}}';
	var generatemenucontrolr= '{{ route("hgeneratemenucontrol") }}';
	var deleteitemmenur= '{{ route("hdeleteitemmenu") }}';
	var deletemenugr= '{{ route("hdeletemenug") }}';
	var createnewmenur= '{{ route("hcreatenewmenu") }}';
	var csrftoken="{{ csrf_token() }}";
	var menuwr = "{{ url()->current() }}";
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': csrftoken
		}
	});
</script>
<script type="text/javascript" src="{{asset('vendor/harimayco/laravel-menu/assets/scripts.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/harimayco/laravel-menu/assets/scripts2.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/harimayco/laravel-menu/assets/menu.js')}}"></script>
@endpush
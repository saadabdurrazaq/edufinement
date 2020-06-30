@extends("layouts.app")

@section("title") Users list @endsection 

@section('index-admin-list')
{{ Breadcrumbs::render('list-applicants-approved') }}
@endsection

@section("content")
    
  @if(session('status'))
  <div class="alert alert-success">
    {{session('status')}}
  </div>
  @endif 

  <div id="flash-message">
    @if( Session::has("success") )
    <div class="alert alert-success alert-block" role="alert" id="success-div">
      <button class="close" data-dismiss="alert"></button>
      {{ Session::get("success") }}
    </div>
    @endif
    @if( Session::has("error") )
    <div class="alert alert-danger alert-block" role="alert" id="success-div">
      <button class="close" data-dismiss="alert"></button>
      {{ Session::get("error") }}
    </div>
    @endif
  </div>

  @section("loader")
    <div class="whole-page-overlay" id="whole_page_loader">
      <img class="center-loader" src="{{ asset('public/images/loader.svg') }}" alt=""  width="50" height="50">
    </div>
  @endsection

  <div class="card card-secondary">
      <div class="card-header">
          <h3 class="card-title">List of Qualified Father</h3>
      </div>
      <div class="card-body">
          <div class="row" style="margin-top:-20px;">
            <div class="col-md-12 menu">
              <nav class="navecation" style="margin-left:-40px;margin-top:20px;">
                <ul id="navi">
                  <li><a class="menu" href="{{route('father-registrars.index')}}">All ({{$count}})</a></li>
                  <li><a class="menu" href="{{route('father-registrars.pending')}}">Pending ({{$countPending}})</a></li>
                  <li><a class="menu" href="{{route('father-registrars.showeligible')}}">Eligible ({{$eligibleStatus}})</a></li>
                  <li><a class="menu {{(request()->is('father-registrars*')) ? 'current' : '' }}" href="{{route('father-registrars.showapproved')}}">Qualified ({{$activeStatus}})</a></li>
                  <li><a class="menu" href="{{route('father-registrars.showrejected')}}">Rejected ({{$inactiveStatus}})</a></li>          
                </ul>
              </nav>
            </div>
          </div>
          <hr>
            <div style="float:left;padding-top:3px;padding-right:4px;">Show</div>
            <div style="width:73px;float:left;margin-bottom:7px;">
              <select id="pagination" class="form-control select2bs4 select2-hidden-accessible" style="width:73px;"" data-select2-id="17" tabindex="-1" aria-hidden="true">
                <option value="5" @if($items == 5) selected @endif data-select2-id="19">5</option>
                <option value="10" @if($items == 10) selected @endif data-select2-id="38">10</option>
                <option value="25" @if($items == 25) selected @endif data-select2-id="39">25</option>
                <option value="50" @if($items == 50) selected @endif data-select2-id="40">50</option>
                <option value="100" @if($items == 100) selected @endif data-select2-id="41">100</option>
                <option value="250" @if($items == 250) selected @endif data-select2-id="42">250</option>
              </select>
            </div>
            <div style="float:left;padding-top:3px;padding-left:4px;padding-right:10px;">entries</div>
            <div style="float:right;">
              <form action="">
              <div class="input-group input-group-sm" style="width:215px;">
                <input type="text" value="{{Request::get('keyword')}}" name="keyword" class="form-control float-right" placeholder="Search by name or email">
                <div class="input-group-append">
                  <button type="submit" value="Filter" class="btn btn-default"><i class="fas fa-search"></i></button>
                </div>
              </div>
              </form>
            </div>
            <div class="panel-body table-responsive" style="overflow:hidden;">
                <table class="table table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        <th><b>No</b></th>
                        <th><b>Name</b></th>
                        <th><b>Email</b></th>
                        <th><b>Roles</b></th>
                        <th><b>Childrens</b></th>
                        <th><b>Child Status</b></th>
                        <th><b>Approved Date</b></th>
                        <th><b>Action</b></th>
                    </tr>
                </thead>
                <tbody>
        @foreach($data as $key => $user)
          <tr>
            <td style="text-align:center;"><input type="checkbox" id="select" class="sub_chk" data-id="{{$user->id}}" value="{{$user->id}}" name="selected_values[]"/></td>
            <td>{{ $user->id }}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>
              @if(!empty($user->getRoleNames()))
                @foreach($user->getRoleNames() as $v)
                  <label class="badge badge-success">{{ $v }}</label>
                @endforeach
              @endif
            </td>
            <td>
              <?php $elements = array(); ?>
              @foreach($user->student_registrars as $category)
                <?php $elements[] = '<a href=" '.route('student-registrars.show', $category->id).' "> '.$category->name.' </a>'; ?>
              @endforeach
              <?php echo implode(',<br>', $elements); ?>
            </td>
            <td>
              @if($user->status == "Pending")
                <span class="badge badge-warning">
                  {{$user->status}}
                </span>
              @endif 
              @if($user->status == "Eligible")
                <span class="badge badge-success">
                  {{$user->status}}
                </span>
              @endif  
              @if($user->status == "Qualified")
                <span class="badge badge-success">
                  {{$user->status}}
                </span>
              @endif  
              @if($user->status == "Rejected")
              <span class="badge badge-danger">
                {{$user->status}}
              </span>
              @endif
            </td>
            <td>{{$echoDate}}</td>
            <td>
              <a href="{{ route('father-registrars.show', $user->id) }}" class="btn btn-primary btn-sm">Detail</a>
            </td>
          </tr>
        @endforeach 
        </tbody>
                </table>
              <div class="row">
                  <div class="col"></div>
                  <div class="col text-right"></div>
              </div>
              <div class="row">
                    <div class="col">
                      <div class="float-left" style="margin-top:15px;">{{$showData}}</div>
                    </div>
                    <div class="col">
                        <div class="float-right" style="margin-top:7px;">{{$data->appends(Request::all())->links()}}</div>
                    </div>
              </div>
              
            </div> <!-- panel-body table-responsive -->
      </div> <!--card body-->
      <div class="card-footer">
            Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
            the plugin.
          </div> 
  </div> <!--card card-secondary-->
@endsection
@section('crud-js') <!--terkait dengan kode@yield('crud-js') di app.blade.php-->
<script>
  $('.alert-success').fadeIn().delay(900).fadeOut();

  //Show entries
  document.getElementById('pagination').onchange = function() { 
    $("#whole_page_loader").show();
    window.location = "{{URL::route('father-registrars.showapproved')}}?items=" + this.value; 
  }; 

  function confirmHold() {
    if(confirm('Hold this applicant?')) {
      $("#whole_page_loader").show();
      $("#submitReject").submit();
    } else {
      return false;
    }
  }

  //Checkbox
  $('#select-all').on('click', function(e) {
    if($(this).is(':checked',true))  {
        $(".sub_chk").prop('checked', true);  
    } else {  
        $(".sub_chk").prop('checked',false);  
    }  
  });

  //Multiple trash and delete
  $('.deactivate_all').on('change', function(e) {
      if($(this).val() == "deactivateAll") {
        var allVals = [];  
        $(".sub_chk:checked").each(function() {  
            allVals.push($(this).attr('data-id'));
        });  

        if(allVals.length <= 0)  {  
          alert("Please select row."); 
        }  
        else {  
            var check = confirm("Are you sure you want to hold these row(s)?");  
            if(check == true){  

                var join_selected_values = allVals.join(","); 

                $.ajax({
                    url: '{{ url('father-registrarsHoldAll') }}',
                    type: 'get',
                    data: 'ids='+join_selected_values,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function(){
                      $("#whole_page_loader").show();
                    },
                    success: function(data) {
                      if (data['success']) {
                           alert(data['success']);
                            $(".sub_chk:checked").each(function() {  
                              $(this).parents("tr").remove();
                              window.location = "{{URL::route('father-registrars.pending')}}"; 
                            });
                            $("#whole_page_loader").hide();
                        } 
                        else if (data['error']) {
                          $("#whole_page_loader").hide();
                            alert(data['error']);
                        } 
                        else {
                          $("#whole_page_loader").hide();
                            alert('Whoops Something went wrong!!');
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });

              $.each(allVals, function( index, value ) {
                  $('table tr').filter("[data-row-id='" + value + "']").remove();
              });
            }  
        }  

      }  //if($(this).val()=="deactivateAll")
  });
</script>
@endsection
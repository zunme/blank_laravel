
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="/stisla/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/izitoast/dist/css/iziToast.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="/stisla/assets/css/style.css">
  <link rel="stylesheet" href="/stisla/assets/css/components.css">
  <style>
  div.dataTables_wrapper div.dataTables_length select {
    width: 66px;
  }
  .swal2-title {
    font-size: 1.675em !important;
  }
  
  </style>
  @yield('css')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>

        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="/stisla/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->email }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="/adm">admin</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="/adm">Adm</a>
          </div>
          <ul class="sidebar-menu">

          @foreach($adminMenus as $index => $menu)
            @if(!empty($menu['nav_title']))
                <li class="menu-header">
                    {{ $menu['nav_title'] }}
                </li>
            @endif

            @if(!empty($menu['sub']))
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><span>{{ $menu['title'] }}</span></a>
                <ul class="dropdown-menu">
                    @foreach($menu['sub'] as $subIndex => $subMenu)
                        <li>
                            <a class="nav-link @if($nowMenu === $index && $nowPosition === $subIndex) active @endif " href="{{ $subMenu['url'] }}">
                                {{ $subMenu['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                </li>
            @else
                <li class=" @if($nowMenu === $index) active @endif ">
                    <a class="nav-link" href="{{ $menu['url'] }}">
                        <!--i class="far fa-user"></i-->
                        <span>{{ $menu['title'] }}</span>
                    </a>
                </li>
            @endif
        @endforeach
              
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
      
        @yield('content')
      
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2021 <div class="bullet"></div> {{ env('APP_NAME', 'ADMIN') }}
        </div>
        <div class="footer-right">
          1.0.1
        </div>
      </footer>
    </div>
  </div>



  <form id="logout-form" action="/logout" method="POST" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  </form>

  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="modal_default_body">

    </div>
  </div>
</div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="/stisla/assets/js/stisla.js"></script>

  <!-- JS Libraies -->
  <script src="/stisla/node_modules/moment/moment.js"></script>
  <script src="/stisla/node_modules/moment/locale/ko.js"></script>

  <script src="/stisla/node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="/stisla/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="/stisla/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>
  
  <script src="/assets/js/handlebars.js"></script>
  
  <!--script src="/stisla/node_modules/sweetalert/dist/sweetalert.min.js"></script-->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="/stisla/node_modules/izitoast/dist/js/iziToast.min.js"></script>

  <!-- summer note -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

  <!-- Template JS File -->
  <script src="/stisla/assets/js/scripts.js"></script>
  <script src="/stisla/assets/js/custom.js"></script>

<!-- echo -->
<script>
        window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';
</script>
  
<script src="//{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT')}}/socket.io/socket.io.js"></script>
<script src="{{ asset('js/laravelecho.js') }}" defer></script>
      
<script type="text/javascript">
  console.log ( "ready")
    document.addEventListener("DOMContentLoaded", function(){
        var i = 0;
        //App\Events\SendMessage에 정의되어있는 수신받을 채널명.
        window.Echo.channel('room_database_roominfo')
        //수신받을 클래스 명.
         .listen('SendMessage', (data) => {
            i++;
            $("#notification").append('<div class="alert alert-success">'+i+'.'+data.title+'</div>');
        	console.log(data);
        });
    });
    
</script>


  <!-- Page Specific JS File -->
<script>
 moment.locale("ko");
 var datatable_lang_kor = {
        "decimal" : "",
        "emptyTable" : "데이터가 없습니다.",
        "info" : "_START_ - _END_ (총 _TOTAL_ )",
        "infoEmpty" : "0",
        "infoFiltered" : "(전체 _MAX_ 명 중 검색결과)",
        "infoPostFix" : "",
        "thousands" : ",",
        "lengthMenu" : "_MENU_ 개씩 보기",
        "loadingRecords" : "로딩중...",
        "processing" : "처리중...",
        "search" : "검색 : ",
        "zeroRecords" : "검색된 데이터가 없습니다.",
        "paginate" : {
            "first" : "첫 페이지",
            "last" : "마지막 페이지",
            "next" : "다음",
            "previous" : "이전"
        },
        "aria" : {
            "sortAscending" : " :  오름차순 정렬",
            "sortDescending" : " :  내림차순 정렬"
        }
    };

function ajaxErrorST(jqXHR ){
  $('.loading_wrap').hide();

  if(jqXHR.status != 422 && jqXHR.status != 500 ) {
    iziToast.error({
        message: '잠시후에 이용해주세요',
        position: 'topRight'
    });
    console.log ( jqXHR  )
    return;
  }

  var msg ;
  var exception ;
  if (jqXHR.responseJSON ) {
    msg = jqXHR.responseJSON.errors;
    exception = jqXHR.responseJSON.exception;
  }

    if(msg) {
      for(key in msg) {
      if(msg.hasOwnProperty(key)) {
        if(key.indexOf('.') < 0 ) {
          $('input[name='+key+']').focus();
        }

        if ( $.isNumeric( key )) {
          iziToast.error({
            message: msg,
            position: 'topRight'
          });
        } else {
          iziToast.error({
            message: msg[key][0],
            position: 'topRight'
          });
        }
        break;
      }
    }
    } else {
      iziToast.error({
        message: '시스템 오류입니다',
        position: 'topRight'
      });
    }
}    
function pop_tpl( size, id , data, title ){
  if ( typeof id =='undefined') return false;
  var availsize = ['sm', 'lg', 'xl']
  if ( !availsize.includes(size) ) size='default';

  var template = Handlebars.compile( $( "#"+id ).html() );
  $("#modal_default_body" ).html ( template(data) );
  $( "#defaultModal" ).modal('handleUpdate')
  $( "#defaultModal" ).modal('show')
}
</script>
@yield('script')
</body>
</html>

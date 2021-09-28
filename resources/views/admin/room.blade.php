@extends('admin.layouts.app')

@section('content')

<div class="row">
    <div class="col-12">
    <div class="card">
        <div class="card-header">
        <h4>Room Config</h4>
        <div class="section-header-breadcrumb">
            <span onClick="add()" class="btn btn-icon btn-danger"><i class="fas fa-plus"></i></span>
        </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover dt-responsive display nowrap" id="datatable">
                    <thead>
                      <tr>
                        <th class="text-center">
                            #
                        </th>
                        <th>Title</th>
                        <th>방갯수</th>
                        <th>입장인원</th>
                        <th>당첨인원</th>
                        <th>입장료</th>
                        <th>취소수수료</th>
                        <th>당첨금(%)</th>
                        <th>마켓팅수당(%)</th>
                        <th>플랜수당(%)</th>
                        <th>시간</th>
                        <th>순서</th>
                        <th>사용여부</th>
                        <th>수정</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection


@section('script')
@verbatim
<script id="updateTpl" type="text/template">
      <div class="modal-header" >
        <h5 class="modal-title" id="modal_default_label">[{{name}}]</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
        <form id='updateform' onsubmit="return false">
            
            {{#unless (checkempty id) }}
              <input type="hidden" name="id" value="{{id}}">
            {{/unless}}
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-3 col-form-label">Title</label>
              <div class="col-sm-9">
                <input type="text" name="name" class="form-control" value="{{name}}">
              </div>
            </div>


            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">방갯수</label>
              <div class="col-sm-3">
                <input type="text" name="num_of_rooms" class="form-control" value="{{num_of_rooms}}">
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">입장인원</label>
              <div class="col-sm-3">
                <input type="text" name="member_per_room" class="form-control" value="{{member_per_room}}">
              </div>
              <div class="col-sm-2"></div>
              <label for="inputEmail3" class="col-sm-2 col-form-label">당첨인원</label>
              <div class="col-sm-3">
                <input type="text" name="num_of_winners" class="form-control" value="{{num_of_winners}}">
              </div>              
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">입장료</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="admission_fee" value="{{admission_fee}}">
                  <div class="input-group-append">
                    <div class="input-group-text">$</div>
                  </div>
                </div>
              </div>
              <div class="col-sm-2"></div>
              <label for="inputEmail3" class="col-sm-2 col-form-label">취소수수료</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="cancellation_fee" value="{{cancellation_fee}}">
                  <div class="input-group-append">
                    <div class="input-group-text">$</div>
                  </div>
                </div>
              </div>              
            </div>
            
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">당첨금</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="winnings" value="{{winnings}}">
                  <div class="input-group-append">
                    <div class="input-group-text">%</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">마켓팅수당</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="marketing_allowance" value="{{marketing_allowance}}">
                  <div class="input-group-append">
                    <div class="input-group-text">%</div>
                  </div>
                </div>
              </div>
              <div class="col-sm-2"></div>
              <label for="inputEmail3" class="col-sm-2 col-form-label">플랜수당</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="plan_allowance" value="{{plan_allowance}}">
                  <div class="input-group-append">
                    <div class="input-group-text">%</div>
                  </div>
                </div>
              </div>              
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">시간</label>
              <div class="col-sm-3">
                <div class="input-group mb-2">
                  <input type="text" class="form-control text-right" name="interval_min" value="{{interval_min}}">
                  <div class="input-group-append">
                    <div class="input-group-text">분</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-3 col-form-label">다음회차시작</label>
              <div class="col-sm-5">
                <input type="text" name="next_game_at" class="form-control datetimepicker" value="{{next_game_at}}">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-form-label col-sm-2">사용여부</label>
              <div class="col-sm-3">
                <select name="is_use"class="form-control form-control-sm">
                  <option value="N" {{#if (isEqual is_use 'N') }}selected{{/if}}>사용안함</option>
                  <option value="Y" {{#if (isEqual is_use 'Y') }}selected{{/if}}>사용</option>
                </select>
              </div>
            </div>



        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onClick="default_form_prc({'form':'updateform', 'url':'/adm/rooms/save','reload':datatable})">Save changes</button>
      </div>
</script>
@endverbatim

<script>
let datatable;
$(document).ready(function() {
  datatable = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "autoWidth": true,
        "responsive": true,
        "language" : datatable_lang_kor,
        "lengthMenu": [10, 5],
        "order": [[ 11, "asc" ]],
        "ajax": {
          'url' : "/adm/rooms/list",
          'data' : function (data){
          }
        },
        "columnDefs": [
          {"targets": [ 0 ],"visible": false,"searchable": false},
          {"targets": [ 1 ],"searchable": true,"sortable":true},
          {"targets": [ 2 ],"searchable": false,"sortable":false},
        ],
        "columns" : [
          {"data" : "id"},
          {"data" : "name"},
          {"data" : "num_of_rooms"},
          {"data" : "member_per_room"},
          {"data" : "num_of_winners"},
          {"data" : "admission_fee"},
          {"data" : "cancellation_fee"},
          {"data" : "winnings"},
          {"data" : "marketing_allowance"},
          {"data" : "plan_allowance"},
          {"data" : "interval_min",
                  "render": function( data, type, row, meta) {
                      return `${data} 분`
                  }
          },

          {"data" : "sort_np"},
          {"data" : "is_use"},
          {"data" : "id",
                  "render": function( data, type, row, meta) {
                      return `<span class="btn btn-danger" onclick="update(this)">수정</span>`
                  }
          },
        ],

        "initComplete": function(settings, json) {
          $("#datatable").css("width","inherit")
          var textBox = $('#datatable_filter label input');
          textBox.unbind();
          textBox.bind('keyup input', function(e) {
              if(e.keyCode == 8 && !textBox.val() || e.keyCode == 46 && !textBox.val()) {
                  // do nothing ¯\_(ツ)_/¯
              } else if(e.keyCode == 13 || !textBox.val()) {
                datatable.search(this.value).draw();
              }
          });          
        },
        "drawCallback": function( settings ) {
        },
        "preDrawCallback": function( settings ) {
        },
    })

  });
function add() {
    let data =  {}
    pop_tpl( 'lg', 'updateTpl',data,'')
}
function update(btn) {
    let data =  datatable.row($(btn).closest('tr')).data();
    pop_tpl( 'lg', 'updateTpl',data,'')
}
function savedefault(){
  $.ajax({
        url:"/adm/rooms/save",
        method:"post",
        data:$("#updateform").serialize(),
        dataType:'JSON',
        success:function(res)
        {
          iziToast.info({
              message: '수정되었습니다.',
              position: 'topRight'
          });
          datatable.ajax.reload(null, false);
          $('#defaultModal').modal('hide');
        },
        error: function ( err ){
            ajaxErrorST(err)
        }
    }); 
}
</script>
@endsection
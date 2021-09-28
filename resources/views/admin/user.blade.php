@extends('admin.layouts.app')

@section('content')

<div class="row">
    <div class="col-12">
    <div class="card">
        <div class="card-header">
        <h4>유저리스트</h4>
        <!--div class="section-header-breadcrumb">
            <span onClick="add()" class="btn btn-icon btn-danger"><i class="fas fa-plus"></i></span>
        </div-->
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover dt-responsive display nowrap" id="datatable">
                    <thead>
                      <tr>
                        <th class="text-center">
                            #
                        </th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>tel</th>
                        <th>국적</th>
                        <th>상태</th>
                        <th>point</th>
                        <th>가용 point</th>
                        <th>추천인코드</th>
                        <th>추천인</th>
                        <th>추천회원수</th>
                        <th>가입일</th>
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
        <h5 class="modal-title" id="modal_default_label">[{{user_id}}]</h5>
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
              <label for="inputEmail3" class="col-sm-3 col-form-label">전화번호</label>
              <div class="col-sm-9">
                <input type="email" name="tel" class="form-control" value="{{tel}}">
              </div>
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-3 col-form-label">국적</label>
              <div class="col-sm-9">
                <select name="national">
                    {{#each langsArr}}
                    <option value='{{@key}}' {{#if (isEqual ../national @key) }}selected{{/if}} >{{this.label}}</option>
                    {{/each}}
                </select>
              </div>
            </div>
  
            <select name="user_level">
                {{#each levelsArr}}
                <option value='{{this.level}}' {{#if (isEqual ../user_level this.level) }}selected{{/if}} >{{this.label}}</option>
                {{/each}}
            </select>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onClick="default_form_prc({'form':'updateform', 'url':'/adm/user/save','reload':datatable})">Save changes</button>
      </div>
</script>
@endverbatim

<script>
let datatable;
let levels = @json($levels);
let langs = @json($langs);

$(document).ready(function() {
  datatable = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "autoWidth": true,
        "responsive": true,
        "language" : datatable_lang_kor,
        "lengthMenu": [10, 5],
        "order": [[ 0, "desc" ]],
        "ajax": {
          'url' : "/adm/user/list",
          'data' : function (data){
          }
        },
        "columnDefs": [
          {"targets": [ 0 ],"visible": false,"searchable": false},
          {"targets": [ 1,2,3,4 ],"searchable": true,"sortable":true},
          {"targets": [ 6 ],"searchable": false,"sortable":true},
          {"targets": [ '_all' ],"searchable": false,"sortable":false},
        ],
        "columns" : [
          {"data" : "id"},
          {"data" : "user_id"},
          {"data" : "email"},
          {"data" : "tel"},
          
          {"data" : "national",
                  "render": function( data, type, row, meta) {
                      if( typeof langs[ data ] == 'undefined') return '-'
                      return langs[ data ].label;
                }
          },
          {"data" : "user_level",
                  "render": function( data, type, row, meta) {
                      if( typeof levels['level_' + data ] == 'undefined') return '-'
                      return levels['level_' + data ].label;
                }
          },
          {"data" : "points"},
          {"data" : "avail"},
          {"data" : "rcmnd_code"},

          {"data" : "parent.email",
                  "render": function( data, type, row, meta) {
                      if( typeof data == 'undefined') return ''
                      return `${data}`;
                }
          },
          {"data" : "children",
                  "render": function( data, type, row, meta) {
                      if( data == null) return '0'
                      return data.length;
                }
          },
          {"data" : "created_at",
                "render": function( data, type, row, meta) {
                    if(data == null) return ''
                    var date = moment( data );
                    return date.local().format('LLL')
                }
            },          
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
    data['levelsArr'] = levels;
    data['langsArr'] = langs;

    console.log (data )
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
@extends('layouts.app', ['title' => "Расписание звонков", 'tab' => 'rings_schedule_edit'])
@section('content')
    <!-- Top navbar -->
    @include('layouts.headers.empty')

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                    <h3 class="mb-0">Расписание звонков</h3>
                            </div>
                            <!--<div class="col-4 text-right">
                                <a href="#" class="btn btn-sm btn-primary">Группы</a>
                            </div>-->
                        </div>
                    </div>
                    <div id="groups-container"></div>
                    <div class="col-12"></div>
                    <div class="datatable-container">
                        <table id="schedules-datatable">

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('assets/datatables/DataTables-1.10.24/js/jquery.dataTables.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Buttons-1.7.0/js/dataTables.buttons.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Select-1.3.3/js/dataTables.select.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Responsive-2.2.7/js/dataTables.responsive.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/AltEditor/dataTables.altEditor.free.min.js')}}" ></script>
    <script type="text/javascript">
        $(document).ready(function (){
            var columnDefs = [
                {
                    data: "id",
                    title: "ИД",
                    type: "readonly"
                },
                {
                    data: 'groups_str',
                    title: "Группы",
                    type: 'readonly'
                }
            ];

            $.fn.dataTable.ext.buttons.edit = {
                extend: 'selectedSingle',
                text: 'Редактировать',
                action: function (e, dt, node, config){
                    let rings = schedulesTable.rows({selected: true}).data()[0];

                    window.location.href = '/schedule/edit/rings/' + rings['id'];
                }
            };

            var schedulesTable = $("#schedules-datatable").DataTable({
                sPaginationType: 'full_numbers',
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                ajax: {
                    url: '{{route('ajax.schedule.rings')}}',
                    dataSrc: ''
                },
                altEditor: true,
                columns: columnDefs,
                dom: 'Bfrtip',
                select: 'single',
                responsive: true,
                buttons:[
                    'edit',
                    {
                        name: 'add',
                        text: 'Создать'
                    },
                    {
                        extend: 'selected',
                        name: 'delete',
                        text: 'Удалить'
                    },
                    {
                        text: 'Обновить',
                        name: 'refresh'
                    }
                ],
                onAddRow: function (datatable, rowdata, success, error) {
                    rowdata._token = '{{csrf_token()}}';
                    $.ajax({
                        url: '{{route('ajax.schedule.rings.create')}}',
                        type: 'PUT',
                        data: rowdata,
                        success: success,
                        error: error
                    });
                },
                onDeleteRow: function (datatable, rowdata, success, error) {
                    $.ajax({
                        url: '/ajax/schedule/rings/'+rowdata.id,
                        type: 'DELETE',
                        data: { _token: '{{csrf_token()}}' },
                        success: success,
                        error: error
                    });
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .datatable-container{
            margin: 10px;
        }
    </style>
    <link rel="stylesheet" href="{{asset('assets/datatables/DataTables-1.10.24/css/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Buttons-1.7.0/css/buttons.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Select-1.3.3/css/select.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Responsive-2.2.7/css/responsive.dataTables.min.css')}}"/>
@endpush

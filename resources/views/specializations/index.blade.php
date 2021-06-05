@extends('layouts.app', ['title' => "Группы пользователей", 'tab' => 'specializations'])
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
                                <h3 class="mb-0">Специальности</h3>
                            </div>
                            <!--<div class="col-4 text-right">
                                <a href="#" class="btn btn-sm btn-primary">Группы</a>
                            </div>-->
                        </div>
                    </div>
                    <div id="groups-container"></div>
                    <div class="col-12"></div>
                    <div class="datatable-container">
                        <table id="groups-datatable">

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
    <script type="text/javascript" src="{{asset('assets/datatables/JSZip-2.5.0/jszip.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/pdfmake-0.1.36/pdfmake.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/pdfmake-0.1.36/vfs_fonts.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Buttons-1.7.0/js/buttons.html5.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Buttons-1.7.0/js/buttons.print.js')}}" ></script>
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
                    data: "name",
                    title: "Наименование"
                },
                {
                    data: 'description',
                    title: "Описание"
                }
            ];

            $.fn.dataTable.ext.buttons.groups = {
                extend: 'selectedSingle',
                text: 'Группы',
                action: function (e, dt, node, config){
                    let specialization = specializationsTable.rows({selected: true}).data()[0];

                    window.location.href = '{{route("specializations")}}/' + specialization['id'] + "/groups";
                }
            };

            var specializationsTable = $("#groups-datatable").DataTable({
                sPaginationType: 'full_numbers',
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                ajax: {
                    url: '{{route('ajax.specializations')}}',
                    dataSrc: ''
                },
                columns: columnDefs,
                dom: 'Bfrtip',
                select: 'single',
                responsive: true,
                altEditor: true,
                buttons:[

                    {
                        text: 'Создать',
                        name: 'add'
                    },
                    {
                        extend: 'selected',
                        text: 'Изменить',
                        name: 'edit'
                    },
                    {
                        extend: 'selected',
                        text: 'Удалить',
                        name: 'delete'
                    },
                    'groups',
                    {
                        text: 'Обновить',
                        name: 'refresh'
                    },
                    @if(!isset($_COOKIE['mobile']) || $_COOKIE['mobile'] != 'true')
                        'excel',
                    {
                        extend: 'csv',
                        charset: 'UTF-8',
                        bom: true
                    },
                    'pdf',
                    'print'
                    @endif

                ],
                onAddRow: function (datatable, rowdata, success, error) {
                    rowdata._token = '{{csrf_token()}}';
                    $.ajax({
                        url: '{{route('ajax.specializations.put')}}',
                        type: 'PUT',
                        data: rowdata,
                        success: success,
                        error: error
                    });
                },
                onDeleteRow: function (datatable, rowdata, success, error) {
                    rowdata._token = '{{csrf_token()}}';
                    $.ajax({
                        url: '{{route('ajax.specializations.delete')}}',
                        type: 'DELETE',
                        data: rowdata,
                        success: success,
                        error: error
                    });
                },
                onEditRow: function (datatable, rowdata, success, error) {
                    rowdata._token = '{{csrf_token()}}';
                    $.ajax({
                        url: '{{route('ajax.specializations.post')}}',
                        type: 'POST',
                        data: rowdata,
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

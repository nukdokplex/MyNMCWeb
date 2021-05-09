@extends('layouts.app', ['title' => "Пользователи", 'tab' => 'users'])
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
                                <h3 class="mb-0">Пользователи</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{route('groups')}}" class="btn btn-sm btn-primary">Группы</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datatable-container">
                            <table id="users-datatable">

                            </table>
                        </div>
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
    <script type="text/javascript" src="{{asset('assets/select2/js/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/select2/js/i18n/ru.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function (){
            var availableRoles = JSON.parse('{!! json_encode($available_roles, JSON_UNESCAPED_UNICODE) !!}');

            //Check server-side to understand why...
            //Or you just searching for holes?
            //If yes why are you here?

            var columnDefs = [
                {
                    data: "id",
                    title: "ИД",
                    type: "readonly"
                },
                {
                    data: "name",
                    title: "Имя пользователя",
                    unique: true,
                },
                {
                    data: 'email',
                    title: "E-Mail"
                },
                {
                    data: 'password',
                    title: 'Пароль',
                    visible: false,
                    searchable: false
                },
                {
                    data: 'role',
                    title: 'Привилегии',
                    type: 'select',
                    options: availableRoles,
                    select2: {
                        width: '100%',
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in availableRoles)) return null;
                        return availableRoles[data];
                    }
                },
                {
                    data: 'created_at',
                    title: 'Создан',
                    type: "readonly"
                },
                {
                    data: 'updated_at',
                    title: 'Изменен',
                    type: "readonly"
                }
            ];

            var usersTable = $("#users-datatable").DataTable({
                sPaginationType: 'full_numbers',
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                ajax: {
                    url: '{{route('ajax.users.detailed')}}',
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
                    {
                        text: 'Обновить',
                        name: 'refresh'
                    },
                    'excel',
                    {
                        extend: 'csv',
                        charset: 'UTF-8',
                        bom: true
                    },
                    'pdf',
                    'print'
                ],
                onAddRow: function (datatable, rowdata, success, error) {
                    console.log(rowdata);
                    $.ajax({
                        url: '{{route('ajax.users.create', ['_token' => csrf_token()])}}',
                        type: 'PUT',
                        data: JSON.stringify(rowdata),
                        success: success,
                        error: error
                    });
                },
                onDeleteRow: function (datatable, rowdata, success, error) {
                    console.log(rowdata);
                    $.ajax({
                        url: '{{route('ajax.users.delete', ['_token' => csrf_token()])}}',
                        type: 'DELETE',
                        data: JSON.stringify(rowdata),
                        success: success,
                        error: error
                    });
                },
                onEditRow: function (datatable, rowdata, success, error) {
                    console.log(rowdata);
                    $.ajax({
                        url: '{{route('ajax.users.update', ['_token' => csrf_token()])}}',
                        type: 'POST',
                        data: JSON.stringify(rowdata),
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
    <link rel="stylesheet" href="{{asset('assets/select2/css/select2.min.css')}}"/>
@endpush

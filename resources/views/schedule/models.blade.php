@extends('layouts.app', ['title' => "Список доступных расписаний по $_model_str", 'tab' => 'schedule'])
@section('content')
    @include('layouts.headers.empty')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Список доступных расписаний по {{$_model_str}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill flex-column flex-sm-row">
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'group' ? 'active' : '' }}" href="{{ $_model == 'group' ? '' : route('schedule.models', ['model_type' => 'group']) }}">Группы</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'teacher' ? 'active' : '' }}" href="{{ $_model == 'teacher' ? '' : route('schedule.models', ['model_type' => 'teacher']) }}">Преподаватели</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'auditory' ? 'active' : '' }}" href="{{ $_model == 'auditory' ? '' : route('schedule.models', ['model_type' => 'auditory']) }}">Аудитории</a>
                            </li>
                        </ul>
                        <div class="m-1 mt-4">
                            <table id="models-datatable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript" src="{{asset('assets/datatables/DataTables-1.10.24/js/jquery.dataTables.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Select-1.3.3/js/dataTables.select.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/RowGroup-1.1.2/js/dataTables.rowGroup.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/datatables/Responsive-2.2.7/js/dataTables.responsive.min.js')}}" ></script>

    <script type="text/javascript">
        $(document).ready(function (){
            var models = JSON.parse('{!! json_encode($models) !!}');
            //Project started to rise and shine!
            //Get your hands out of this code!
            //It's beautiful as is!

            var route = '{{route('schedule.model', ['model_type' => $_model, ':model_id'])}}';

            var columnDefs = [
                {
                    data: "name",
                    title: "Наименование",
                    render: function (data, type, row){

                        let url = route.replace(':model_id', row['id'])
                        return '<a class="btn btn-outline-secondary" href="'+url+'">'+data+'</a>';
                    },
                    @if($_model == 'group')
                        orderable: false,
                    @endif
                },
                @if($_model == 'group')
                    {
                        data: 'specialization',
                        title: 'Специальность',
                        visible: false
                    },

                @endif
            ];

            var modelsTable = $("#models-datatable").DataTable({
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                data: models,
                columns: columnDefs,
                dom: 'Bfrtip',
                displayLength: 25,
                @if($_model == 'group')
                    rowGroup: {
                        dataSrc: 'specialization'
                    },
                    order: [[1, 'asc']],
                @endif
            });


        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/datatables/DataTables-1.10.24/css/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Buttons-1.7.0/css/buttons.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Select-1.3.3/css/select.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Responsive-2.2.7/css/responsive.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/RowGroup-1.1.2/css/rowGroup.dataTables.min.css')}}"/>
@endpush

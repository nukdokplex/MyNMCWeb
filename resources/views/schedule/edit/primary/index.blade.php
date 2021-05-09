@extends('layouts.app', ['title' => "Основные расписания занятий", 'tab' => 'primary_schedule_edit'])
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
                                    <h3 class="mb-0">Основные расписания занятий</h3>
                            </div>
                            <!--<div class="col-4 text-right">
                                <a href="#" class="btn btn-sm btn-primary">Группы</a>
                            </div>-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datatable-container">
                            <table id="groups-datatable">

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
    <script type="text/javascript" src="{{asset('assets/datatables/AltEditor/dataTables.altEditor.free.min.js')}}" ></script>
    <script type="text/javascript">
        $(document).ready(function (){
            var columnDefs = [
                {
                    data: "id",
                    title: "ИД"
                },
                {
                    data: 'name',
                    title: "Наименование"
                },
                {
                    data: 'description',
                    title: "Описание"
                }
            ];

            $.fn.dataTable.ext.buttons.schedule = {
                extend: 'selectedSingle',
                text: 'Редактировать',
                action: function (){
                    let group = groupsTable.rows({selected: true}).data()[0];

                    window.location.href = '/schedule/edit/primary/'+group.id+'/odd/mon';
                }
            };

            var groupsTable = $("#groups-datatable").DataTable({
                sPaginationType: 'full_numbers',
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                ajax: {
                    url: '{{route('ajax.groups')}}',
                    dataSrc: ''
                },
                altEditor: true,
                columns: columnDefs,
                dom: 'Bfrtip',
                select: 'single',
                responsive: true,
                buttons:[
                    'schedule',
                ],

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

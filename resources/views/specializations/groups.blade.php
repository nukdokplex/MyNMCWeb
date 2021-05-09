@extends('layouts.app', ['title' => "Группы специальности «" . $specialization->name . "»", 'tab' => 'specializations'])
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
                                <h3 class="mb-0">Группы специальности «{{$specialization->name}}»</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{route('specializations')}}" class="btn btn-sm btn-primary">Вернуться к специальностям</a>
                            </div>
                        </div>
                    </div>
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
    <script type="text/javascript" src="{{asset('assets/datatables/AltEditor/dataTables.altEditor.free.min.js')}}" ></script>
    <script type="text/javascript">
        $(document).ready(function (){
            var columnDefs = [
                {
                    data: "id",
                    title: "ИД"
                },
                {
                    data: "name",
                    title: "Наименование"
                },
                {
                    data: 'description',
                    title: 'Описание'
                }
            ];

            $.ajax({
                url: '{{route('ajax.specialization.groups', ['specialization' => $specialization->id])}}',
                dataType: 'json',
                success: function (selectedGroups, textStatus, jqXHR) {
                    $.fn.dataTable.ext.buttons.apply = {
                        text: 'Внести изменения',
                        action: function (e, dt, node, config){

                            let groups = groupsTable.rows({selected: true}).data();

                            if (groups.length === 0){
                                groups = [];

                            }
                            else{
                                let groups_temp = [];

                                for (i = 0; i < groups.length; i++){
                                    groups_temp.push(groups[i]);
                                }

                                groups = groups_temp;
                            }

                            $.ajax({
                                url: '{{route('ajax.specialization.groups.post', ['specialization' => $specialization->id, '_token' => csrf_token()])}}',
                                type: 'POST',
                                data: JSON.stringify({ groups: groups }),
                                processData: false,
                                success: function(){
                                    alert('Успешно сохранено!');
                                    window.location.reload(true);
                                },
                                error: function (jqXHR, textStatus, errorThrown){
                                    alert('Произошла ошибка при ассинхронном сохранении данных: '+textStatus);
                                    console.error(jqXHR, textStatus, errorThrown)
                                }
                            });

                            console.log(groups);
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
                        columns: columnDefs,
                        dom: 'Bfrtip',
                        select: {
                            style: 'multi'
                        },
                        responsive: true,
                        buttons:[
                            'apply'
                        ],
                        fnInitComplete: function (settings, json) {
                            selectedGroups.forEach(group => {
                                for (i = 0; i < groupsTable.rows().count(); i++){
                                    if (groupsTable.rows(i).data()[0].id === group.id){
                                        groupsTable.rows(i).select();
                                    }
                                }
                            });
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown){
                    alert('Произошла ошибка при ассинхронной загрузке данных: '+textStatus);
                    console.error(jqXHR, textStatus, errorThrown)
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

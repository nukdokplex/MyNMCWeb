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
                            <div class="col-4 text-right">
                                <a href="#" id="save-button" class="btn btn-sm btn-primary">Сохранить</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>ПРИМЕЧАНИЕ: к сожалению, из-за нехватки средств, времени и людей сообщения об ошибках ограниченны. Мы не можем показать, какое поле Вы указали неверно, поэтому при ошибке, Вам придется выяснять ее причину самостоятельно. Спасибо за понимание!</strong></p>
                        <div class="table-responsive">
                            <table class="table align-items-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Номер</th>
                                        <th scope="col">Начинается в</th>
                                        <th scope="col">Прерывается в</th>
                                        <th scope="col">Продолжается в</th>
                                        <th scope="col">Кончается в</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rings->rings as $ring)
                                        <tr>
                                            <td>{{$ring->session_number}}</td>
                                            <td><input id="start-timepicker-{{$ring->session_number}}" class="timepicker" value="{{$ring->starts_at}}" /></td>
                                            <td><input id="interrupt-timepicker-{{$ring->session_number}}" class="timepicker" value="{{$ring->interrupts_at}}" /></td>
                                            <td><input id="continue-timepicker-{{$ring->session_number}}" class="timepicker" value="{{$ring->continues_at}}" /></td>
                                            <td><input id="end-timepicker-{{$ring->session_number}}" class="timepicker" value="{{$ring->ends_at}}" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow mt-3">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Группы</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{route('groups')}}" target="_blank" class="btn btn-sm btn-primary">Управление группами</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datatable-container">
                            <table id="groups-datatable"></table>
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
    <script type="text/javascript" src="{{asset('assets/timepicker/jquery.timepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function (){
            $('.timepicker').timepicker({
                timeFormat: 'HH:mm',
                interval: 5,
                minTime: '07:00',
                maxTime: '23:59',
                defaultTime: '05:00',
                startTime: '05:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });

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
                url: '/ajax/schedule/rings/{{$rings->id}}/groups',
                dataType: 'json',
                success: function (selectedGroups, textStatus, jqXHR) {

                    $('#save-button').click(function () {
                        save();
                    });

                    function save() {
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

                        console.log(groups);

                        let schedule = [];

                        for (i = 1; i < 9; i++){
                            schedule.push({
                                session_number: i,
                                starts_at: $('#start-timepicker-'+i).val(),
                                interrupts_at: $('#interrupt-timepicker-'+i).val(),
                                continues_at: $('#continue-timepicker-'+i).val(),
                                ends_at: $('#end-timepicker-'+i).val(),
                            });
                        }

                        $.ajax({
                            url: '/ajax/schedule/rings/{{$rings->id}}?_token={{csrf_token()}}',
                            type: 'POST',
                            data: JSON.stringify({ groups: groups, rings: schedule }),
                            success: function(){
                                alert('Успешно сохранено!');
                                window.location.href = '{{route('schedule.edit.rings')}}';
                            },
                            error: function (jqXHR, textStatus, errorThrown){
                                alert('Произошла ошибка при ассинхронном сохранении данных: '+textStatus);
                                console.error(jqXHR, textStatus, errorThrown)
                            }
                        });
                    }

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
        input.timepicker {
            width: 75px !important;
        }
        div.ui-timepicker li > .ui-corner-all{
            font-size: 12px;
        }
    </style>
    <link rel="stylesheet" href="{{asset('assets/timepicker/jquery.timepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/datatables/DataTables-1.10.24/css/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Buttons-1.7.0/css/buttons.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Select-1.3.3/css/select.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Responsive-2.2.7/css/responsive.dataTables.min.css')}}"/>
@endpush

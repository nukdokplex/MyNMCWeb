@extends('layouts.app', ['title' => "Основное расписание для «".$group->name."»", 'tab' => 'primary_schedule_edit'])
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
                                <h3 class="mb-0">Основное расписание для «{{$group->name}}»</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>ПРИМЕЧАНИЕ: к сожалению, из-за нехватки средств, времени и людей сообщения об ошибках ограниченны. Мы не можем показать, какое поле Вы указали неверно, поэтому при ошибке, Вам придется выяснять ее причину самостоятельно. Спасибо за понимание!</strong></p>
                        <div class=" d-flex justify-content-center">
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => 'odd', 'day_of_week' => $day_of_week])}}" class="btn {{ $week_number == 'odd' ? 'btn-primary' : 'btn-secondary' }}">Нечетная</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => 'even', 'day_of_week' => $day_of_week])}}" class="btn {{ $week_number == 'even' ? 'btn-primary' : 'btn-secondary' }}">Четная</a>
                        </div>
                        <div class="d-flex mt-1 justify-content-center">
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'mon'])}}" class="btn {{ $day_of_week == 'mon' ? 'btn-primary' : 'btn-secondary' }}">ПН</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'tue'])}}" class="btn {{ $day_of_week == 'tue' ? 'btn-primary' : 'btn-secondary' }}">ВТ</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'wed'])}}" class="btn {{ $day_of_week == 'wed' ? 'btn-primary' : 'btn-secondary' }}">СР</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'thu'])}}" class="btn {{ $day_of_week == 'thu' ? 'btn-primary' : 'btn-secondary' }}">ЧТ</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'fri'])}}" class="btn {{ $day_of_week == 'fri' ? 'btn-primary' : 'btn-secondary' }}">ПТ</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'sat'])}}" class="btn {{ $day_of_week == 'sat' ? 'btn-primary' : 'btn-secondary' }}">СБ</a>
                            <a href="{{route('schedule.edit.primary', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => 'sun'])}}" class="btn {{ $day_of_week == 'sun' ? 'btn-primary' : 'btn-secondary' }}">ВС</a>
                        </div>
                        <div class="datatable-container mt-3 m-1">
                            <table id="primary-schedule-datatable">

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
        $(document).ready(function () {

            var subjects = JSON.parse('{!! json_encode($subjects, JSON_UNESCAPED_UNICODE) !!}');
            var teachers = JSON.parse('{!! json_encode($teachers, JSON_UNESCAPED_UNICODE) !!}');
            var auditories = JSON.parse('{!! json_encode($auditories, JSON_UNESCAPED_UNICODE) !!}');

            var columnDefs = [
                {
                    data: 'number',
                    title: 'Номер занятия',
                    type: 'readonly'
                },
                {
                    data: 'subject',
                    title: 'Предмет',
                    type: 'select',
                    options: subjects,
                    select2: {
                        width: '100%'
                    },
                    render: function (data, type, row, meta) {
                        if (data == null || data === -1 || !(data in subjects))
                            return null;
                        return subjects[data];
                    }
                },
                {
                    data: 'teacher',
                    title: 'Преподаватель',
                    type: 'select',
                    options: teachers,
                    select2: {
                        width: '100%'
                    },
                    render: function (data, type, row, meta) {
                        if (data == null || data === -1 || !(data in teachers))
                            return null;
                        return teachers[data];
                    }
                },
                {
                    data: 'auditory',
                    title: 'Аудитория',
                    type: 'select',
                    options: auditories,
                    select2: {
                        width: '100%'
                    },
                    render: function (data, type, row, meta) {
                        if (data == null || data === -1 || !(data in auditories))
                            return null;
                        return auditories[data];
                    }
                }
            ];

            $.fn.dataTable.ext.buttons.apply = {
                text: 'Внести изменения',
                action: function (e, dt, node, config) {
                    let schedule = scheduleTable.rows().data();

                    let schedule_temp = [];

                    for (i = 0; i < schedule.length; i++){
                        schedule_temp.push({
                            number: schedule[i].number,
                            subject: parseInt(schedule[i].subject),
                            teacher: parseInt(schedule[i].teacher),
                            auditory: parseInt(schedule[i].auditory),
                        });
                    }
                    schedule = schedule_temp;

                    $.ajax({
                        url: '{{route('ajax.schedule.edit.primary.update', ['group' => $group->id, 'week_number' => $week_number, 'day_of_week' => $day_of_week, '_token' => csrf_token()])}}',
                        type: 'POST',
                        data: JSON.stringify({schedule: schedule}),
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
                }
            };


            var scheduleTable = $('#primary-schedule-datatable').DataTable({
                paging: false,
                language: {
                    url: '{{asset('assets/datatables/localization_ru.json')}}'
                },
                data: JSON.parse('{!! json_encode($primary_schedule, JSON_UNESCAPED_UNICODE) !!}'),
                columns: columnDefs,
                dom: 'Bfrtip',
                select: 'single',
                responsive: true,
                altEditor: true,

                buttons:[
                    'apply',
                    {
                        extend: 'selected',
                        text: 'Изменить',
                        name: 'edit'
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
            });
        });
    </script>
@endpush

@push('styles')
    <style>

    </style>
    <link rel="stylesheet" href="{{asset('assets/datatables/DataTables-1.10.24/css/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Buttons-1.7.0/css/buttons.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Select-1.3.3/css/select.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Responsive-2.2.7/css/responsive.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/select2/css/select2.min.css')}}"/>
@endpush

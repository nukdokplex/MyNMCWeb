@extends('layouts.app', ['title' => "Текущее расписание занятий для «" . $model->name . "»", 'tab' => 'schedule_edit'])

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
                                <h3 class="mb-0">Текущее расписание занятий для «{{$model->name}}»</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <strong>Выберите неделю:</strong>
                        <p>
                            @foreach($available_weeks as $available_week)
                                <button type="button" class="btn {{$available_week['active'] ? 'btn-primary' : 'btn-secondary'}} mt-1 mb-1" onclick="window.location.href='{{route('schedule.edit.edit', ['model' => $_model, 'id' => $model->id, 'week' => $available_week['number']])}}';">
                                    {{date_format($available_week['dates'][0], 'j.n.y')}}&nbsp;-&nbsp;{{date_format($available_week['dates'][6], 'j.n.y')}}{{isWeekOdd($available_week['dates'][0]) ? '`' : '``'}}
                                </button>
                            @endforeach
                        </p>
                        <!-- Oh, I'm so tired... -->
                        <!-- I really don't believe that I've come all the way from creating an idea to putting it into practice. -->
                        @foreach($current_week['dates'] as $date)
                            <div class="mt-3">
                                <h3>{{mb_ucfirst(getDayOfWeekName(intval(date_format($date, 'N'))-1))}}:</h3>
                                <div class="mt-1 ml-1 mr-1">
                                    <table class="schedule-datatable" id="schedule-datatable-{{strtolower(date_format($date, 'D'))}}">

                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if($_model == 'group')
                    <div class="card shadow mt-3">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Синхронизация с основным расписанием</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Вы можете синхронизировать текущее расписание занятий с <a href="{{route('schedule.edit.primary.groups')}}" target="_blank">Основным расписанием</a>. Для этого Вам нужно выбрать диапазон дат, расписание дней в котором будут изменены на Основное расписание включительно, которое Вы настроили ранее.</strong></p>
                            <div class="mt-2 d-flex align-content-center">
                                <input type="text" id="sync-daterange">
                                <button class="btn btn-icon btn-primary ml-2" style="height: 30px; padding: 0 20px;" type="button" id="sync-btn">
                                    <span class="btn-inner--icon"><i class="mdi mdi-18px mdi-autorenew"></i></span>
                                    <span class="btn-inner--text">Синхронизировать</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
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
    <script type="text/javascript" src="{{asset('assets/select2/js/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/select2/js/i18n/ru.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/daterangepicker-master/moment.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/daterangepicker-master/daterangepicker.js')}}" ></script>

    <script type="text/javascript">
        $(document).ready(function (){
            @if($_model == 'group')
                $('#sync-daterange').daterangepicker({
                    timePicker: false,
                    startDate: '{{date_format($start_date, 'd.m.Y')}}',
                    endDate: '{{date_format($start_date->add(new DateInterval('P1W')), 'd.m.Y')}}',
                    minDate: '{{date_format($start_date, 'd.m.Y')}}',
                    maxDate: '{{date_format($end_date, 'd.m.Y')}}',
                    locale: {
                        format: 'DD.MM.YYYY',
                        daysOfWeek: [
                            "ВС",
                            "ПН",
                            "ВТ",
                            "СР",
                            "ЧТ",
                            "ПТ",
                            "СБ"
                        ],
                        monthNames: [
                            "Январь",
                            "Февраль",
                            "Март",
                            "Апрель",
                            "Май",
                            "Июнь",
                            "Июль",
                            "Август",
                            "Сентябрь",
                            "Октябрь",
                            "Ноябрь",
                            "Декабрь"
                        ],
                        firstDay: 1,
                        applyLabel: "Применить",
                        cancelLabel: "Отмена",
                        fromLabel: "С",
                        toLabel: "До",
                        customRangeLabel: "Выборочно",
                        weekLabel: "Н",
                    }
                });

                $('#sync-btn').click(function () {
                    let startDate = $('#sync-daterange').data('daterangepicker').startDate.format('DD.MM.YYYY');
                    let endDate = $('#sync-daterange').data('daterangepicker').endDate.format('DD.MM.YYYY');

                    $.ajax({
                        url: '{{route('ajax.schedule.edit.sync', ['_token' => csrf_token()])}}',
                        type: 'POST',
                        data: JSON.stringify({
                            start_date: startDate,
                            end_date: endDate,
                            group_id: '{{$model->id}}'
                        }),
                        success: function (){
                            alert('Асинхронная синхронизация прошла успешно!');
                           // location.reload();
                        },
                        error: function (){
                            alert('Произошла ошибка при попытке асинхронной синхронизации!');
                           // location.reload();
                        }
                    });
                });
            @endif

            let teachersTemp = JSON.parse('{!! json_encode($teachers)!!}');
            let subjectsTemp = JSON.parse('{!! json_encode($subjects)!!}');
            let auditoriesTemp = JSON.parse('{!! json_encode($auditories)!!}');
            let groupsTemp = JSON.parse('{!! json_encode($groups)!!}');

            var teachersOptions = {};
            var subjectsOptions = {};
            var auditoriesOptions = {};
            var groupsOptions = {};

            teachersTemp.forEach((teacher) => {
                teachersOptions[teacher.id.toString()] = teacher.name;
            });

            auditoriesTemp.forEach((auditory) => {
                auditoriesOptions[auditory.id.toString()] = auditory.name;
            });

            subjectsTemp.forEach((subject) => {
                subjectsOptions[subject.id.toString()] = subject.name;
            });

            groupsTemp.forEach((group) => {
                groupsOptions[group.id.toString()] = group.name;
            });

            var subgroupsOptions = [
                { id: -1, text: '(все)', selected: true },
                { id: 1, text: '1'},
                { id: 2, text: '2'},
                { id: 3, text: '3'},
                { id: 4, text: '4'},
            ];
            var numberOptions = {1: "1", 2: "2", 3: "3", 4: "4", 5: "5", 6: "6", 7: "7", 8: "8"};

            let columnDefs = [
                {
                    data: 'id',
                    visible: false,
                    searchable: false,
                    type: 'hidden'
                },
                {
                    data: "number",
                    title: "Номер",
                    type: 'select',
                    options: numberOptions,
                    select2: {
                        width: '100%',
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in numberOptions)) return null;
                        return numberOptions[data];
                    }
                },
                {
                    data: "group_id",
                    title: "Группа",
                    type: 'select',
                    options: groupsOptions,
                    select2: {
                        width: '100%'
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in groupsOptions)) return null;
                        return groupsOptions[data];
                    }
                },
                {
                    data: 'subgroup',
                    title: 'Подгруппа',
                    type: 'select',
                    //options: subgroupsOptions,
                    select2: {
                        width: '100%',
                        data: subgroupsOptions
                    },
                    render: function (data, type, row, meta){


                        if (data == null) return subgroupsOptions.find(x => x.id === -1).text;
                        return subgroupsOptions.find(x => x.id === data).text;

                    }
                },
                {
                    data: 'teacher_id',
                    title: 'Преподаватель',
                    type: 'select',
                    options: teachersOptions,
                    select2: {
                        width: '100%'
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in teachersOptions)) return null;
                        return teachersOptions[data];

                    },

                },
                {
                    data: 'subject_id',
                    title: 'Предмет',
                    type: 'select',
                    options: subjectsOptions,
                    select2: {
                        width: '100%',
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in subjectsOptions)) return null;
                        return subjectsOptions[data];

                    },

                },
                {
                    data: 'auditory_id',
                    title: 'Аудитория',
                    type: 'select',
                    options: auditoriesOptions,
                    select2: {
                        width: '100%',
                    },
                    render: function (data, type, row, meta){
                        if (data == null || !(data in auditoriesOptions)) return null;
                        return auditoriesOptions[data];
                    },

                },

            ];

            let daysOfWeek = [
                'mon',
                'tue',
                'wed',
                'thu',
                'fri',
                'sat',
                'sun'
            ];

            let datesOfWeek = {
                @foreach($current_week['dates'] as $date)
                '{{strtolower(date_format($date, 'D'))}}': '{{strtolower(date_format($date, 'd.m.Y'))}}',
                @endforeach
            };

            let datatables = {};

            daysOfWeek.forEach((dayOfWeek) => {
                datatables[dayOfWeek] = $('.schedule-datatable#schedule-datatable-'+dayOfWeek).DataTable({
                    language: {
                        url: '{{asset('assets/datatables/localization_ru.json')}}'
                    },
                    ajax: {
                        url: '{!! route('ajax.schedule.edit', ['_token' => csrf_token(), 'model' => $_model, 'model_id' => $model->id])!!}&date='+encodeURI(datesOfWeek[dayOfWeek]),
                        dataSrc: ''
                    },
                    columns: columnDefs,
                    order: [[ 1, "asc" ],[ 3, "asc" ]],
                    dom: 'Bfrtip',
                    select: 'single',
                    responsive: true,
                    altEditor: true,
                    paging: false,
                    info: false,
                    searching: false,
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
                    ],
                    onAddRow: function (datatable, rowdata, success, error) {
                        rowdata.date = datesOfWeek[dayOfWeek];
                        console.log(rowdata);
                        $.ajax({
                            url: '{{route('ajax.schedule.edit.create', ['_token' => csrf_token()])}}',
                            type: 'PUT',
                            data: JSON.stringify(rowdata),
                            success: success,
                            error: error
                        });
                    },
                    onDeleteRow: function (datatable, rowdata, success, error) {
                        console.log(rowdata);
                        $.ajax({
                            url: '{{route('ajax.schedule.edit.delete', ['_token' => csrf_token()])}}',
                            type: 'DELETE',
                            data: JSON.stringify(rowdata),
                            success: success,
                            error: error
                        });
                    },
                    onEditRow: function (datatable, rowdata, success, error) {
                        rowdata.date = datesOfWeek[dayOfWeek];
                        console.log(rowdata);
                        $.ajax({
                            url: '{{route('ajax.schedule.edit.update', ['_token' => csrf_token()])}}',
                            type: 'POST',
                            data: JSON.stringify(rowdata),
                            success: success,
                            error: error
                        });
                    }
                });
            });

        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/datatables/DataTables-1.10.24/css/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Buttons-1.7.0/css/buttons.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Select-1.3.3/css/select.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/datatables/Responsive-2.2.7/css/responsive.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/daterangepicker-master/daterangepicker.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/select2/css/select2.min.css')}}"/>
@endpush

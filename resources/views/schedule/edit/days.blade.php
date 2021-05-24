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

                    </div>
                </div>
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
                            <button class="btn btn-icon btn-primary ml-2" style="height: 30px; padding: 0 20px;" type="button">
                                <span class="btn-inner--icon"><i class="mdi mdi-18px mdi-autorenew"></i></span>
                                <span class="btn-inner--text">Синхронизировать</span>
                            </button>
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
    <script type="text/javascript" src="{{asset('assets/daterangepicker-master/moment.min.js')}}" ></script>
    <script type="text/javascript" src="{{asset('assets/daterangepicker-master/daterangepicker.js')}}" ></script>

    <script type="text/javascript">
        $(document).ready(function (){
            $('#sync-daterange').daterangepicker({
                timePicker: false,
                startDate: Date.parse('{{date_format($start_date, 'Y-m-d')}}'),
                endDate: Date.parse('{{date_format($end_date, 'Y-m-d')}}'),
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
    <link rel="stylesheet" href="{{asset('assets/daterangepicker-master/daterangepicker.css')}}"/>
@endpush

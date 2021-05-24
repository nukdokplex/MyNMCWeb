@extends('layouts.app', ['title' => "Пользователи группы «" . $group->name . "»", 'tab' => 'groups'])
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
                                <h3 class="mb-0">Пользователи группы «{{$group->name}}»</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{route('groups')}}" class="btn btn-sm btn-primary">Группы</a>
                            </div>
                        </div>
                    </div>
                    <div id="groups-container"></div>
                    <div class="col-12"></div>
                    <div class="datatable-container">
                        <table id="users-datatable">

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
            //Hello.
            //What are you searching for here?
            //Everything working well.
            //Some DataTables tricks used.
            //Rows preselecting using AJAX as well as possible.

            //I really hate JS!
            //01.05.2021 5:53 AM

            var columnDefs = [
                {
                    data: "id",
                    title: "ИД"
                },
                {
                    data: "name",
                    title: "Имя пользователя"
                },
                {
                    data: 'email',
                    title: "E-Mail"
                },
                {
                    data: 'role',
                    title: "Роль",
                    render: function (data){
                        let translations = {
                            'guest' : 'Гость',
                            'student' : 'Студент',
                            'teacher' : 'Преподаватель',
                            'administrator' : 'Администрация',
                            'system architect' : 'Системный архитектор'
                        };
                        return translations[data];
                    }
                }
            ];

            $.ajax({
                url: '{{route('ajax.group.users', ['group' => $group->id])}}',
                dataType: 'json',
                success: function (selectedUsers, textStatus, jqXHR) {
                    $.fn.dataTable.ext.buttons.apply = {
                        text: 'Внести изменения',
                        action: function (e, dt, node, config){

                            let users = usersTable.rows({selected: true}).data();

                            if (users.length === 0){
                                users = [];

                            }
                            else{
                                let users_temp = [];

                                for (i = 0; i < users.length; i++){
                                    users_temp.push(users[i]);
                                }

                                users = users_temp;
                            }

                            $.ajax({
                                url: '{{route('ajax.group.users.post', ['group' => $group->id, '_token' => csrf_token()])}}',
                                type: 'POST',
                                data: JSON.stringify({ users: users }),
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

                            console.log(users);



                        }
                    };

                    var usersTable = $("#users-datatable").DataTable({
                        sPaginationType: 'full_numbers',
                        language: {
                            url: '{{asset('assets/datatables/localization_ru.json')}}'
                        },
                        ajax: {
                            url: '{{route('ajax.users.by_role', ['roles' => 'teacher,student'])}}',
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
                            selectedUsers.forEach(user => {
                                for (i = 0; i < usersTable.rows().count(); i++){
                                    console.log(usersTable.rows(i).data()[0].id === user.id)
                                    if (usersTable.rows(i).data()[0].id === user.id){
                                        usersTable.rows(i).select();
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

@extends('layouts.app', ['title' => "Расписание занятий", 'tab' => 'schedule'])
@section('content')
    @include('layouts.headers.empty')
    <div class="container-fluid" style="margin-top: -9rem;">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Доступные расписания</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill flex-column flex-sm-row">
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'group' ? 'active' : '' }}" href="{{route('schedule.models', ['model_type' => 'group']) }}">Группы</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'teacher' ? 'active' : '' }}" href="{{route('schedule.models', ['model_type' => 'teacher']) }}">Преподаватели</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-sm-3 mb-md-0 {{ $_model == 'auditory' ? 'active' : '' }}" href="{{route('schedule.models', ['model_type' => 'auditory']) }}">Аудитории</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Расписание занятий для {{$_model_str}} «{{$model['name']}}»</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mt-1 table-responsive">
                            <table class="table align-items-center" id="schedule-datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" rowspan="2"></th>
                                        <th scope="col" rowspan="2">#</th>
                                        <th class="text-center"  scope="col" colspan="{{$max_subgroup}}">Подгруппы</th>
                                    </tr>
                                    <tr>

                                        @for($i = 1; $i < $max_subgroup+1; $i++)
                                        <th class="text-center" >{{$i}}</th>
                                        @endfor
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($dates as $date)
                                        <?php
                                        /** @var DateTimeImmutable $date */
                                        /** @var array $schedule_sessions */
                                        /** @var string $_model */
                                        switch ($_model){
                                            case 'group': $fields = ['teacher', 'auditory']; break;
                                            case 'teacher': $fields = ['group', 'auditory']; break;
                                            case 'auditory': $fields = ['group', 'teacher']; break;
                                        }
                                        $date_str = $date->format('d.m.Y');
                                        $s3 = array_keys(array_column($schedule_sessions, 'date'), $date_str);
                                        /** @var int $max_subgroup */



                                        if (empty($s3)) {
                                            echo '<tr>';
                                            echo '<td rowspan="1">' . $date_str . '<br>' . mb_ucfirst(getDayOfWeekName(intval($date->format('N'))-1)) . '</td>';
                                            echo '<td></td>';
                                            /** @var int $max_subgroup */
                                            echo '<td class="text-center"  colspan="'.$max_subgroup.'">Отдых</td>';
                                            echo '</tr>';
                                        }
                                        else {
                                           // $max_number = $schedule_sessions[$s3[count($s3)-1]]['number'];
                                            $max_number = 8;
                                            $schedule = new \Arrayy\Arrayy($schedule_sessions);
                                            $is_date_rendered = false;
                                            for ($number = 1; $number < $max_number+1; $number++){
                                                echo '<tr>';
                                                if (!$is_date_rendered){
                                                    echo '<td rowspan="'.$max_number.'">' . $date_str . '<br>' . mb_ucfirst(getDayOfWeekName(intval($date->format('N'))-1)) . '</td>';
                                                    $is_date_rendered = true;
                                                }
                                                echo '<td>'.$number.'</td>';
                                                if ($schedule->first()->get('number') != $number){

                                                    /** @var int $max_subgroup */
                                                    echo '<td colspan="'.$max_subgroup.'"></td>';
                                                    continue;
                                                }
                                                /** @var int $max_subgroup */
                                                for ($subgroup = 1; $subgroup < $max_subgroup + 1; $subgroup++){
                                                    $session = $schedule->first();
                                                    if ($session->get('subgroup') == null){
                                                        echo '<td class="text-center" colspan="'.$max_subgroup.'">'.$session->get($fields[0]). ', ' . $session->get($fields[1]).'</td>';
                                                        $schedule->delete(0);
                                                        break;
                                                    }
                                                    if ($session->get('subgroup') != $subgroup){
                                                        echo '<td></td>';
                                                        continue;
                                                    }
                                                    echo '<td class="text-center" >'.$session->get($fields[0]). ', ' . $session->get($fields[1]).'</td>';
                                                    $schedule->delete(0);
                                                }
                                                echo '</tr>';
                                            }
                                        }
                                        ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript">
        $(document).ready(function (){
            //I'm so tired...
            //But I must continuing...

        });
    </script>
@endpush

@push('styles')

@endpush

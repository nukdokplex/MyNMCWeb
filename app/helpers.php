<?php

function getMonthName($month){
    return [
        'январь',
        'февраль',
        'март',
        'апрель',
        'май',
        'июнь',
        'июль',
        'август',
        'сентябрь',
        'октябрь',
        'ноябрь',
        'декабрь'
    ][$month];
}

function getDayOfWeekName($day){
    return [
        'понедельник',
        'вторник',
        'среда',
        'четверг',
        'пятница',
        'суббота',
        'воскресенье'
    ][$day];
}

function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

function isWeekOdd(DateTimeInterface $date){
    return intval($date->format('W')) % 2 == 1;
}

/**
 * Returns dates in current week number
 *
 * @param int $week
 */
function weekToDates(int $week){
    if ($week < 0 || $week > 29){
        return null;
    }

    $monday = new \DateTimeImmutable('monday this week'); //Tricky!

    $monday = $week == 0 ? $monday : $monday->add(new \DateInterval('P'.$week.'W'));

    $result = [];
    array_push($result, $monday);

    $i = 0;
    while ($i < 6){
        array_push($result, $result[$i]->add(new \DateInterval('P1D')));
        $i++;
    }

    return $result;
}

function getDaysFrom(DateTimeImmutable $date, int $days){
    if ($days == null || $days == 0){
        return [];
    }

    $result = [$date];

    for ($i = 0; $i < $days; $i++){
        array_push($result, $result[$i]->add(new DateInterval('P1D')));
    }

    return $result;
}

function getDateWithTime(DateTimeImmutable $date, string $time){
    return DateTimeImmutable::createFromFormat('d.m.Y H:i:s',
        $date->format('d.m.Y') . ' ' . $time);
}


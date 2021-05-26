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


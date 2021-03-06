<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $auditory_names = [
            "101м",
            "102 Соц.-эконом.дисцип. ",
            "103 Башкир.язык",
            "105 Башкир.язык",
            "106м",
            "107 Учебный кул.цех",
            "109 Тех.кулинар.произв.",
            "10м ПАО",
            "11 Тех.и обор.произ.эл.",
            "116 Иностранный язык",
            "118 Иностранного языка",
            "11м ТТП,ТАСР (Пож. без.)",
            "12 Электротехн.и электр",
            "122 Техн.,микр,санит,гиг",
            "124 пк",
            "13м Автомат.проектир.ТП",
            "14м",
            "15м Техн.элек.свар.плав",
            "16м Охр.труд,ПБ,медик.би",
            "17 Иностранный язык",
            "18 Иностранный язык",
            "18м Механическая",
            "19м Электромонтажная",
            "1м Уч-кулин.кондит.цех",
            "2 УК",
            "200м",
            "201 Безопасность жизн.",
            "201м",
            "202м",
            "203 Дисцип.общепроф.цикл",
            "203м",
            "204 Информатика",
            "204м",
            "205м",
            "206 Техн.электр.работ",
            "206м",
            "207 Тех.обс.и рем.авто",
            "208 Информатика",
            "21 Тех.регул.и КК",
            "21м Монтаж СТСиО",
            "22 Тип.узлы и ср.авт.",
            "22м ТМиРвМЦ",
            "23 Философия",
            "23м Лазерная резка",
            "24 Техн.механ.",
            "24м Электромонтажная",
            "2м, 4м ГОМ",
            "301 История",
            "302 Информатики",
            "303 Информатики",
            "304 Рус.яз.Литература",
            "305 Иностранный язык",
            "306 Гуманит.дисциплины",
            "307 Рус.язык.Литература",
            "308 Тех.и обор.произ.эл.",
            "31 Экономика орг,менед.",
            "32 Учебная бухг.",
            "33 Бух.учет, АФХД и нал",
            "34 Экон.организ.(п/п)",
            "35 ОБД, ВЭД, Ст.и мендж",
            "3м Материаловеден.",
            "401 Биол. и эк.осн.прир.",
            "402 Химии",
            "403 Физика",
            "404 Математика",
            "405 Математика",
            "406 Матем.дисцип.",
            "407 Матем.дисцип.",
            "408 Физика",
            "41 Циф.схем.ММС",
            "42 Програм.СПП ИСР",
            "44 Тех.ср.инф. ПУ СМЭВМ",
            "45 Вычис.техн.и програм",
            "46 КС,ТОСВТ и КС",
            "47 Инженерная графика",
            "48 Инженерная графика",
            "5м ПОПД, соц.псих.",
            "6м Теплоснабж. экс.НИТО",
            "7м Теор.осн.свар.и рез.",
            "8м Автом.техн.процес.",
            "9м Матер.ИМ и ККСС",
            "Дист.Об",
            "ДО",
            "ДО-",
            "ДО.",
            "нефаз",
            "пож.ч.",
            "ПЧ",
            "СП",
            "СП1 1 корпус",
            "СП2 1 корпус",
            "СП3 2 корпус",
            "СП4 2 корпус",
            "стадион",
            "тропа зд.",
            "ч.з.1к.",
            "Ч.З.2К."
        ];

        foreach ($auditory_names as $auditory_name){
            DB::table("auditories")->insert([
                'name' => $auditory_name
            ]);
        }
    }
}

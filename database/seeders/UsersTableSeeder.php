<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teachers = [
            'Азнабаева А.Б.',
            'Акитарова Т.С.',
            'Алехина Э.Д.',
            'Ахматгалиев Р.З.',
            'Ахметов Р.Р.',
            'Баязитова К.Г.',
            'Белоусова Л.А.',
            'Бочарова Е.П.',
            'Бухараева Л.Ф,',
            'Валиев Б.Ф.',
            'Валиева Л.А.',
            'Воробьева З.А.',
            'Вшивкова Э.М.',
            'Гайнуллина Т.И.',
            'Гайсина И.Р.',
            'Галлямов Р.В.',
            'Гарифуллин М.Р.',
            'Гиворг И.Ф.',
            'Гильманова С.К.',
            'Гималтдинова Р.Р.',
            'Давлетханова Г.Н.',
            'Давлятханова Э.М.',
            'Данилова Н.В.',
            'Димитриева О.П.',
            'Жукова А.Р.',
            'Захарьян Е.В.',
            'Зиангирова Л.Д.',
            'Зиязова Г.И.',
            'Имамова Э.Ф.',
            'Исакова С.Е.',
            'Исламова Г.А.',
            'Исламова Е.М.',
            'Исламова Л.М.',
            'Ишимбаев С.В.',
            'Калимуллина О.Ф.',
            'Каримова В.К.',
            'Креклин В.А.',
            'Маликов Т.Ф.',
            'Мансурова З.А.',
            'Матвеева Э.Х.',
            'Мезенцева Н.Г.',
            'Мингалев Н.С.',
            'Миникаева Х.Ф.',
            'Минниахметов Э.В.',
            'Моисеев А.В.',
            'Мрясова О.Л.',
            'Муллазанова Л.А.',
            'Муратова Ч.З.',
            'Муртазин И.Р.',
            'Мухаметшина Р.А.',
            'Мухарямова Р.А.',
            'Набиуллина И.Ф.',
            'Начиналова О.А.',
            'Нуретдинова Г.Ф.',
            'Пестерева А.И.',
            'Платова Н.М.',
            'Подрядов Ю.Б.',
            'Пудовкин Е.А.',
            'Рафикова Г.Ф.',
            'Садртдинова Р.М.',
            'Салимьянова З.Р.',
            'Салихова И.Х.',
            'Саляхова Н.М.',
            'Саяпова Р.И.',
            'Серебряков И.А.',
            'Талипова Р.Р.',
            'Тимербаев Н.Н.',
            'Тимерьянов В.Ф.',
            'Тимирзянов Р.Г.',
            'Тимуршин А.А.',
            'Тонконогий А.В.',
            'Туйкова С.Е.',
            'Усманов Т.К.',
            'Фазлова З.М.',
            'Файзрахманов Ф.Ф.',
            'Файзуллина Э.Т.',
            'Хаирланамова Г.И.',
            'Хамидуллина Ж.А.',
            'Хасанова А.Ф.',
            'Ценева С.Г.',
            'Цеплина Е.А.',
            'Цуварев И.А',
            'Чиганова А.А.',
            'Шагалиева З.М.',
            'Шаймарданов М.М.',
            'Шамагулова Г.Н.',
            'Шангареева Р.С.',
            'Шангареева С.С.',
            'Ширшакова М.В.',
            'Щербакова Д.И.'
        ];

        $teachers_to_apply = [];

        foreach ($teachers as $teacher){
            $i = 0;
            while (true){
                $teacher_email_name = \Transliterate::slugify(preg_split('/\s+/', $teacher, 2)[0]) . ($i == 0 ? '' : $i) . '@nmt.edu.ru';

                //$this->command->info(array_search($teacher_email_name, array_column($teachers_to_apply, 'email')));

                if (array_search($teacher_email_name, array_column($teachers_to_apply, 'email')) == false){
                    break;
                }
                //What the fuck is this? This is smart email generation based on name of prepod...
                $i++;
            }

            array_push($teachers_to_apply, ['name' => $teacher, 'email' => $teacher_email_name]);
        }

        foreach ($teachers_to_apply as $teacher){
            $user = new User();

            $user->name = $teacher['name'];
            $user->email = $teacher['email'];
            $user->email_verified_at = now();
            $user->password = '12345678';

            $user->save();

            $user->assignRole(['teacher']);
            $user->save();
        }

        /*DB::table('users')->insert([
            'name' => 'Титов Виктор',
            'email' => 'webmaster@nukdotcom.ru',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now()
        ]);*/
    }
}

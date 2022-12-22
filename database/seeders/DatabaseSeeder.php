<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Mark;
use App\Models\Mark_modification;
use App\Models\RegisterUser;
use App\Models\Sclass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Database\Factories\RegisterUserFactory;
use Database\Factories\SclassFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $class1a = Sclass::factory()->create([
            'name' => 'A',
            'class_start' => date_create('2021-09-01'),
            'class_end' => date_create('2025-09-01'),
        ]);
        $class2b = Sclass::factory()->create([
            'name' => 'B',
            'class_start' => date_create('2020-09-01'),
            'class_end' => date_create('2024-09-01'),
        ]);
        $class3d = Sclass::factory()->create([
            'name' => 'D',
            'class_start' => date_create('2019-09-01'),
            'class_end' => date_create('2023-09-01'),
        ]);

        $subject_j_polski = Subject::factory()->create([
            'name' => 'język polski',
            'description' => 'język lechicki z grupy zachodniosłowiańskiej (do której należą również czeski, kaszubski, słowacki i języki łużyckie), stanowiącej część rodziny indoeuropejskiej'
        ]);
        $subject_etyka = Subject::factory()->create([
            'name' => 'etyka',
            'description' => 'dział filozofii, zajmujący się badaniem moralności i tworzeniem systemów myślowych, z których można wyprowadzać zasady moralne'
        ]);
        $subject_filozofia = Subject::factory()->create([
            'name' => 'filozofia',
            'description' => 'tłumaczone jako „umiłowanie mądrości”) – systematyczne i krytyczne rozważania na temat podstawowych problemów i idei, dążące do poznania ich istoty, a także do całościowego zrozumienia świata'
        ]);
        $subject_historia = Subject::factory()->create([
            'name' => 'historia',
            'description' => 'dawniej: dziejoznawstwo – nauka humanistyczna i społeczna, która zajmuje się badaniem przeszłości'
        ]);
        $subject_w_f = Subject::factory()->create([
            'name' => 'wychowanie fizyczne',
            'description' => 'zamierzone i świadome działanie nastawione na wspieranie rozwoju fizycznego i zdrowia'
        ]);
        $subject_matematyka = Subject::factory()->create([
            'name' => 'matematyka',
            'description' => ' nauka dostarczająca narzędzi do otrzymywania ścisłych wniosków z przyjętych założeń, zatem dotycząca prawidłowości rozumowania'
        ]);
        $subject_fizyka = Subject::factory()->create([
            'name' => 'fizyka',
            'description' => 'nauka przyrodnicza, zajmująca się badaniem najbardziej fundamentalnych i uniwersalnych właściwości oraz przemian materii i energii, a także oddziaływań między nimi'
        ]);
        $subject_informatyka = Subject::factory()->create([
            'name' => 'informatyka',
            'description' => 'nauka ścisła oraz techniczna zajmująca się przetwarzaniem informacji, w tym również technologiami przetwarzania informacji oraz technologiami wytwarzania systemów przetwarzających informacje'
        ]);
        $subject_j_angielski = Subject::factory()->create([
            'name' => 'język angielski',
            'description' => 'język z grupy zachodniej rodziny języków germańskich, powszechnie używany w Wielkiej Brytanii, jej terytoriach zależnych oraz w wielu byłych koloniach'
        ]);
        $subject_biologia = Subject::factory()->create([
            'name' => 'biologia',
            'description' => 'nauka przyrodnicza zajmująca się badaniem życia i organizmów żywych.'
        ]);
        $subject_chemia = Subject::factory()->create([
            'name' => 'chemia',
            'description' => 'nauka przyrodnicza badająca naturę i właściwości substancji, a zwłaszcza przemiany zachodzące pomiędzy nimi'
        ]);
        $subject_j_niemiecki = Subject::factory()->create([
            'name' => 'język niemiecki',
            'description' => 'język z grupy zachodniej rodziny języków germańskich; w lingwistyce traktuje się go jako grupę kilku języków zachodniogermańskich, które bywają określane jako języki niemieckie'
        ]);
        $subject_j_polski->sclasses()->attach($class1a);
        $subject_etyka->sclasses()->attach($class1a);
        $subject_filozofia->sclasses()->attach($class1a);
        $subject_historia->sclasses()->attach($class1a);
        $subject_w_f->sclasses()->attach($class1a);

        $subject_matematyka->sclasses()->attach($class2b);
        $subject_fizyka->sclasses()->attach($class2b);
        $subject_informatyka->sclasses()->attach($class2b);
        $subject_j_angielski->sclasses()->attach($class2b);
        $subject_w_f->sclasses()->attach($class2b);

        $subject_biologia->sclasses()->attach($class3d);
        $subject_chemia->sclasses()->attach($class3d);
        $subject_fizyka->sclasses()->attach($class3d);
        $subject_j_niemiecki->sclasses()->attach($class3d);
        $subject_w_f->sclasses()->attach($class3d);

        $admin = RegisterUser::factory()->create([
            'name' => 'Anna',
            'surname' => 'Kowalska',
            'email' => 'anna.kowalska@wp.pl',
            'password' => hash('sha256', 'Admin1234!'),
            'isAdmin' => true
        ]);

        $teacher_j_polski = RegisterUser::factory()->create([
            'name' => 'Julian',
            'surname' => 'Tuwim',
            'email' => 'julian.tuwim@wp.pl',
            'password' => hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_j_polski->id,
            'subject_id' => $subject_j_polski->id
        ]);
        $teacher_etyka = RegisterUser::factory()->create([
            'name' => 'Tadeusz',
            'surname' => 'Kotarbiński',
            'email' => 'tadeusz.kotarbinski@wp.pl',
            'password' => hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_etyka->id,
            'subject_id' => $subject_etyka->id
        ]);
        $teacher_filozofia = RegisterUser::factory()->create([
            'name' => 'Józef',
            'surname' => 'Tischner',
            'email' => 'jozef.tischner@wp.pl',
            'password' => hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_filozofia->id,
            'subject_id' => $subject_filozofia->id
        ]);
        $teacher_historia = RegisterUser::factory()->create([
            'name' => 'Norman',
            'surname' => 'Davies',
            'email' => 'norman.davies@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_historia->id,
            'subject_id' => $subject_historia->id
        ]);
        $teacher_w_f = RegisterUser::factory()->create([
            'name' => 'Robert',
            'surname' => 'Lewandowski',
            'email' => 'robert.lewandowski@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_w_f->id,
            'subject_id' => $subject_w_f->id
        ]);
        $teacher_matematyka = RegisterUser::factory()->create([
            'name' => 'Marian',
            'surname' => 'Rejewski',
            'email' => 'marian.rejewski@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_matematyka->id,
            'subject_id' => $subject_matematyka->id
        ]);
        $teacher_fizyka = RegisterUser::factory()->create([
            'name' => 'Marian',
            'surname' => 'Smoluchowski',
            'email' => 'marian.smoluchowski@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_fizyka->id,
            'subject_id' => $subject_fizyka->id
        ]);
        $teacher_informatyka = RegisterUser::factory()->create([
            'name' => 'Tomasz',
            'surname' => 'Czajka',
            'email' => 'tomasz.czajka@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_informatyka->id,
            'subject_id' => $subject_informatyka->id
        ]);
        $teacher_j_angielski = RegisterUser::factory()->create([
            'name' => 'Jerzy',
            'surname' => 'Limon',
            'email' => 'jerzy.limon@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_j_angielski->id,
            'subject_id' => $subject_j_angielski->id
        ]);
        $teacher_biologia = RegisterUser::factory()->create([
            'name' => 'Jędrzej',
            'surname' => 'Śniadecki',
            'email' => 'jedrzej.sniadecki@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_biologia->id,
            'subject_id' => $subject_biologia->id
        ]);
        $teacher_chemia = RegisterUser::factory()->create([
            'name' => 'Maria',
            'surname' => 'Skłodowska-Curie',
            'email' => 'maria.sklodowska-curie@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_chemia->id,
            'subject_id' => $subject_chemia->id
        ]);
        $teacher_j_niemiecki = RegisterUser::factory()->create([
            'name' => 'Zdzisław',
            'surname' => 'Wawrzyniak',
            'email' => 'zdzislaw.wawrzyniak@wp.pl',
            'password' =>
            hash('sha256', 'Test1234!'),
            'isAdmin' => false
        ]);
        Teacher::create([
            'user_id' => $teacher_j_niemiecki->id,
            'subject_id' => $subject_j_niemiecki->id
        ]);
        $class1_student1 = RegisterUser::factory()->create([
            'name' => 'Anna',
            'surname' => 'Drgas',
            'email' => 'anna.drgas@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class1_student1->id,
            'sclass_id' => $class1a->id
        ]);
        $class1_student2 = RegisterUser::factory()->create([
            'name' => 'Helena',
            'surname' => 'Kwiecień',
            'email' => 'helena.kwiecien@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class1_student2->id,
            'sclass_id' => $class1a->id
        ]);
        $class1_student3 = RegisterUser::factory()->create([
            'name' => 'Mateusz',
            'surname' => 'Bartczak',
            'email' => 'mateusz.bartczak@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class1_student3->id,
            'sclass_id' => $class1a->id
        ]);
        $class1_student4 = RegisterUser::factory()->create([
            'name' => 'Filip',
            'surname' => 'Dobrzański',
            'email' => 'filip.dobrzanski@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class1_student4->id,
            'sclass_id' => $class1a->id
        ]);
        $class1_student5 = RegisterUser::factory()->create([
            'name' => 'Arkadiusz',
            'surname' => 'Szymański',
            'email' => 'arkadiusz.szymanski@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class1_student5->id,
            'sclass_id' => $class1a->id
        ]);
        $class2_student1 = RegisterUser::factory()->create([
            'name' => 'Oliwia',
            'surname' => 'Wisłocka',
            'email' => 'oliwia.wislocka@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class2_student1->id,
            'sclass_id' => $class2b->id
        ]);
        $class2_student2 = RegisterUser::factory()->create([
            'name' => 'Iwona',
            'surname' => 'Matuszak',
            'email' => 'iwona.matuszak@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class2_student2->id,
            'sclass_id' => $class2b->id
        ]);
        $class2_student3 = RegisterUser::factory()->create([
            'name' => 'Ewa',
            'surname' => 'Nowaczyk',
            'email' => 'ewa.nowaczyk@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class2_student3->id,
            'sclass_id' => $class2b->id
        ]);
        $class2_student4 = RegisterUser::factory()->create([
            'name' => 'Katarzyna',
            'surname' => 'Jamróz',
            'email' => 'katarzyna.jamroz@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class2_student4->id,
            'sclass_id' => $class2b->id
        ]);
        $class2_student5 = RegisterUser::factory()->create([
            'name' => 'Krzysztof',
            'surname' => 'Nowicki',
            'email' => 'krzysztof.nowicki@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class2_student5->id,
            'sclass_id' => $class2b->id
        ]);
        $class3_student1 = RegisterUser::factory()->create([
            'name' => 'Michał',
            'surname' => 'Matysiak',
            'email' => 'michal.matysiak@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class3_student1->id,
            'sclass_id' => $class3d->id
        ]);
        $class3_student2 = RegisterUser::factory()->create([
            'name' => 'Józef',
            'surname' => 'Michalik',
            'email' => 'jozef.michalik@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class3_student2->id,
            'sclass_id' => $class3d->id
        ]);
        $class3_student3 = RegisterUser::factory()->create([
            'name' => 'Wiktor',
            'surname' => 'Broniecki',
            'email' => 'wiktor.broniecki@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class3_student3->id,
            'sclass_id' => $class3d->id
        ]);
        $class3_student4 = RegisterUser::factory()->create([
            'name' => 'Alina',
            'surname' => 'Bojko',
            'email' => 'alina.bojko@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class3_student4->id,
            'sclass_id' => $class3d->id
        ]);
        $class3_student5 = RegisterUser::factory()->create([
            'name' => 'Aleksandra',
            'surname' => 'Giżyńska',
            'email' => 'aleksandra.gizynska@wp.pl',
            'password' =>
            hash('sha256', 'Uczen1234!'),
            'isAdmin' => false
        ]);
        Student::create([
            'user_id' => $class3_student5->id,
            'sclass_id' => $class3d->id
        ]);
        $activity_praca_klasowa = Activity::create([
            'name' => 'praca klasowa',
            'conversion_factor' => 5
        ]);
        $activity_kartkowka = Activity::create([
            'name' => 'kartkówka',
            'conversion_factor' => 4
        ]);
        $activity_odpowiedz_ustna = Activity::create([
            'name' => 'odpowiedź ustna',
            'conversion_factor' => 3
        ]);
        $activity_zad_dom = Activity::create([
            'name' => 'zadanie domowe',
            'conversion_factor' => 3
        ]);
        $activity_zad_dod = Activity::create([
            'name' => 'zadanie dodatkowe',
            'conversion_factor' => 1
        ]);
        $class1_j_polski_mark1 = Mark::create([
            'user_student_id' => $class1_student1->id,
            'subject_id' => $subject_j_polski->id,
            'moderator_id' => $teacher_j_polski->id,
            'activity_id' => $activity_praca_klasowa->id,
            'mark_datetime' => Carbon::now(),
            'value' => 5
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_j_polski->id,
            'mark_id' => $class1_j_polski_mark1->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class1_j_polski_mark1->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class1_j_polski_mark2 = Mark::create([
            'user_student_id' => $class1_student2->id,
            'subject_id' => $subject_j_polski->id,
            'moderator_id' => $teacher_j_polski->id,
            'activity_id' => $activity_praca_klasowa->id,
            'mark_datetime' => Carbon::now(),
            'value' => 3
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_j_polski->id,
            'mark_id' => $class1_j_polski_mark2->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class1_j_polski_mark2->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class1_j_polski_mark3 = Mark::create([
            'user_student_id' => $class1_student3->id,
            'subject_id' => $subject_j_polski->id,
            'moderator_id' => $teacher_j_polski->id,
            'activity_id' => $activity_praca_klasowa->id,
            'mark_datetime' => Carbon::now(),
            'value' => 4
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_j_polski->id,
            'mark_id' => $class1_j_polski_mark3->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class1_j_polski_mark3->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class1_j_polski_mark4 = Mark::create([
            'user_student_id' => $class1_student4->id,
            'subject_id' => $subject_j_polski->id,
            'moderator_id' => $teacher_j_polski->id,
            'activity_id' => $activity_praca_klasowa->id,
            'mark_datetime' => Carbon::now(),
            'value' => 2
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_j_polski->id,
            'mark_id' => $class1_j_polski_mark4->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class1_j_polski_mark4->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class1_j_polski_mark5 = Mark::create([
            'user_student_id' => $class1_student5->id,
            'subject_id' => $subject_j_polski->id,
            'moderator_id' => $teacher_j_polski->id,
            'activity_id' => $activity_praca_klasowa->id,
            'mark_datetime' => Carbon::now(),
            'value' => 1
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_j_polski->id,
            'mark_id' => $class1_j_polski_mark5->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class1_j_polski_mark5->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class2_fizyka_mark1 = Mark::create([
            'user_student_id' => $class2_student1->id,
            'subject_id' => $subject_fizyka->id,
            'moderator_id' => $teacher_fizyka->id,
            'activity_id' => $activity_kartkowka->id,
            'mark_datetime' => Carbon::now(),
            'value' => 3
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_fizyka->id,
            'mark_id' => $class2_fizyka_mark1->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class2_fizyka_mark1->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class2_fizyka_mark2 = Mark::create([
            'user_student_id' => $class2_student2->id,
            'subject_id' => $subject_fizyka->id,
            'moderator_id' => $teacher_fizyka->id,
            'activity_id' => $activity_kartkowka->id,
            'mark_datetime' => Carbon::now(),
            'value' => 3
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_fizyka->id,
            'mark_id' => $class2_fizyka_mark2->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class2_fizyka_mark2->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class2_fizyka_mark3 = Mark::create([
            'user_student_id' => $class2_student3->id,
            'subject_id' => $subject_fizyka->id,
            'moderator_id' => $teacher_fizyka->id,
            'activity_id' => $activity_kartkowka->id,
            'mark_datetime' => Carbon::now(),
            'value' => 1
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_fizyka->id,
            'mark_id' => $class2_fizyka_mark3->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class2_fizyka_mark3->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class2_fizyka_mark4 = Mark::create([
            'user_student_id' => $class2_student4->id,
            'subject_id' => $subject_fizyka->id,
            'moderator_id' => $teacher_fizyka->id,
            'activity_id' => $activity_kartkowka->id,
            'mark_datetime' => Carbon::now(),
            'value' => 4
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_fizyka->id,
            'mark_id' => $class2_fizyka_mark4->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class2_fizyka_mark4->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class2_fizyka_mark5 = Mark::create([
            'user_student_id' => $class2_student5->id,
            'subject_id' => $subject_fizyka->id,
            'moderator_id' => $teacher_fizyka->id,
            'activity_id' => $activity_kartkowka->id,
            'mark_datetime' => Carbon::now(),
            'value' => 5
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_fizyka->id,
            'mark_id' => $class2_fizyka_mark5->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class2_fizyka_mark5->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class3_chemia_mark1 = Mark::create([
            'user_student_id' => $class3_student1->id,
            'subject_id' => $subject_chemia->id,
            'moderator_id' => $teacher_chemia->id,
            'activity_id' => $activity_odpowiedz_ustna->id,
            'mark_datetime' => Carbon::now(),
            'value' => 2
        ]);
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_chemia->id,
            'mark_id' => $class3_chemia_mark1->id,
            'mark_before_modification' => null,
            'mark_after_modification' => $class3_chemia_mark1->value,
            'modification_reason' => 'dodanie oceny'
        ]);
        $class3_chemia_mark1->value = 3;
        $class3_chemia_mark1->save();
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_chemia->id,
            'mark_id' => $class3_chemia_mark1->id,
            'mark_before_modification' => 2,
            'mark_after_modification' => $class3_chemia_mark1->value,
            'modification_reason' => 'pomyłka przy wpisaniu pierwszej oceny'
        ]);
        $class3_chemia_mark1->value = 4;
        $class3_chemia_mark1->save();
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_chemia->id,
            'mark_id' => $class3_chemia_mark1->id,
            'mark_before_modification' => 3,
            'mark_after_modification' => $class3_chemia_mark1->value,
            'modification_reason' => 'uczeń przygotował się ponownie i poprawił ocenę'
        ]);
        $class3_chemia_mark1->value = 5;
        $class3_chemia_mark1->save();
        Mark_modification::create([
            'modification_datetime' => Carbon::now(),
            'moderator_id' => $teacher_chemia->id,
            'mark_id' => $class3_chemia_mark1->id,
            'mark_before_modification' => 4,
            'mark_after_modification' => $class3_chemia_mark1->value,
            'modification_reason' => 'uczeń przeczytał książkę, o której dokładnie odpowiedział'
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Question\Question;
use App\Models\Question\QuestionItem;
use App\Models\Question\QuestionItemAnswer;
use App\Models\Subject\Subject;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Factory::create();

        for ($i=0; $i < 10; $i++) {
            Subject::create([
                'name'=>$faker->name(),
            ]);
        }

        for ($k=0; $k < 20; $k++) {
            $question = Question::create([
                'name'=>'Modul '.$k + 1,
                'minutes'=>120,
                'subject_id'=>Subject::inRandomOrder()->first()->id,
                'is_random'=>rand(0,1),
                'user_id'=>User::first()->id
            ]);

            for ($j=0; $j < 100; $j++) {
                $question_item = QuestionItem::create([
                    'question_id'=>$question->id,
                    'title'=>$faker->name(),
                    'description'=>$faker->paragraph(),
                    'weight'=>5,
                ]);

                for ($a=0; $a < 5; $a++) {
                    $alphabet = 'A'; // Default alphabet pertama

                        // Cari huruf terakhir yang digunakan pada question_id dan question_item_id yang sama
                    $lastAnswer = QuestionItemAnswer::where('question_id', $question->id)
                                                    ->where('question_item_id', $question_item->id)
                                                    ->orderBy('alphabet', 'desc') // Urutkan berdasarkan alphabet terbalik
                                                    ->first();

                    // Jika ada jawaban sebelumnya, ambil alphabet terakhir dan increment
                    if ($lastAnswer) {
                        $lastAlphabet = $lastAnswer->alphabet;
                        $alphabet = chr(ord($lastAlphabet) + 1); // Increment alphabet
                    }

                    QuestionItemAnswer::create([
                        'question_id'=>$question->id,
                        'question_item_id'=>$question_item->id,
                        'title'=>$faker->name(),
                        'alphabet' => $alphabet,
                    ]);
                }

                $question_item->question_item_answer_id = QuestionItemAnswer::where('question_id', $question->id)->where('question_item_id', $question_item->id)->inRandomOrder()->first()->id;
                $question_item->save();
            }
        }
    }
}

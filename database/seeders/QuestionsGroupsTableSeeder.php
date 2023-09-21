<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        DB::table('questions_groups')->delete();

        DB::table('questions_groups')->insert(array(
            0 =>
            array(
                'id' => 1,
                'en_name' => 'Content and Organization of Course',
                'bn_name' => 'কোর্স এর বিষয়বস্তু এবং কাঠামো',
                'created_at' => '2022-12-23 22:20:56',
                'updated_at' => '2022-12-23 22:20:56',
            ),
            1 =>
            array(
                'id' => 2,
                'en_name' => 'Teaching Methods',
                'bn_name' => 'শিক্ষাদান পদ্ধতি',
                'created_at' => '2022-12-23 22:22:16',
                'updated_at' => '2022-12-23 22:22:16',
            ),
            2 =>
            array(
                'id' => 3,
                'en_name' => 'Teaching-learning Environment',
                'bn_name' => 'শিক্ষাদান এবং শিক্ষণ পরিবেশ',
                'created_at' => '2022-12-23 22:23:50',
                'updated_at' => '2022-12-23 22:23:50',
            ),
            3 =>
            array(
                'id' => 4,
                'en_name' => 'Effectiveness of the Course',
                'bn_name' => 'কোর্সের কার্যকারিতা',
                'created_at' => '2022-12-23 22:25:04',
                'updated_at' => '2022-12-23 22:25:04',
            ),
            4 =>
            array(
                'id' => 5,
                'en_name' => 'Recommendations/Overall Comments',
                'bn_name' => 'প্রস্তাবনা/সামগ্রিক মন্তব্য',
                'created_at' => '2022-12-23 22:25:58',
                'updated_at' => '2022-12-23 22:25:58',
            ),
        ));
    }
}

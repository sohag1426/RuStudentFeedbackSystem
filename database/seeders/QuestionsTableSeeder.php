<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->delete();

        DB::table('questions')->insert([
            0 => [
                'id' => 1,
                'question_no' => 'a)',
                'department_id' => 0,
                'questions_group_id' => 1,
                'en' => 'The objectives of the course were clear and easy to understand.',
                'bn' => 'কোর্সের উদ্দেশ্যগুলি স্পষ্ট এবং সহজবোধ্য ছিল ।',
                'created_at' => '2026-01-22 13:34:05',
                'updated_at' => '2026-01-22 13:20:43',
            ],
            1 => [
                'id' => 2,
                'question_no' => 'b)',
                'department_id' => 0,
                'questions_group_id' => 1,
                'en' => 'Satisfaction with the contents of the course.',
                'bn' => 'কোর্সের বিষয়বস্তুর ব্যাপারে সন্তুষ্টির মাত্রা ।',
                'created_at' => '2026-01-22 13:43:31',
                'updated_at' => '2026-01-22 13:21:29',
            ],
            2 => [
                'id' => 3,
                'question_no' => 'a)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'Teaching methods (lecturer, presentation, group discussion, group work etc.) were participatory.',
                'bn' => 'শিক্ষাদান পদ্ধতি (প্রেজেন্টেশন, গ্রুপওয়ার্ক, গ্রুপ ডিসকাশন, বক্তৃতা ইত্যাদি) অংশগ্রহণমূলক ছিল ।',
                'created_at' => '2026-01-22 13:32:12',
                'updated_at' => '2026-01-22 13:32:12',
            ],
            3 => [
                'id' => 4,
                'question_no' => 'b)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'I enjoyed the teaching methods of the course teacher.',
                'bn' => 'আমি কোর্স শিক্ষকের শিক্ষাদান পদ্ধতি উপভোগ করেছি ।',
                'created_at' => '2026-01-22 13:33:44',
                'updated_at' => '2026-01-22 13:33:44',
            ],
            4 => [
                'id' => 5,
                'question_no' => 'c)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'The course teacher was regular and punctual throughout the course (maintained the class routine, entered and exited the classroom on time).',
                'bn' => 'কোর্স শিক্ষক কোর্সের সময়কালে নিয়মিত এবং সময়নিষ্ঠ ছিলেন ।',
                'created_at' => '2026-01-22 13:34:06',
                'updated_at' => '2026-01-22 13:34:06',
            ],
            5 => [
                'id' => 6,
                'question_no' => 'd)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'The pace of the course was appropriate.',
                'bn' => 'কোর্সের সার্বিক গতি যথাযথ ছিল ।',
                'created_at' => '2026-01-22 13:34:25',
                'updated_at' => '2026-01-22 13:34:25',
            ],
            6 => [
                'id' => 7,
                'question_no' => 'e)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'The teacher explained the course material clearly.',
                'bn' => 'শিক্ষক কোর্সের উপাদান স্পষ্টভাবে ব্যাখ্যা করেছেন ।',
                'created_at' => '2026-01-22 13:34:42',
                'updated_at' => '2026-01-22 13:34:42',
            ],
            7 => [
                'id' => 8,
                'question_no' => 'f)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'The teacher answered students\' questions well.',
                'bn' => 'শিক্ষক শিক্ষার্থীদের প্রশ্নের উত্তর ভালোভাবে দিয়েছেন ।',
                'created_at' => '2026-01-22 13:35:00',
                'updated_at' => '2026-01-22 13:35:00',
            ],
            8 => [
                'id' => 9,
                'question_no' => 'g)',
                'department_id' => 0,
                'questions_group_id' => 2,
                'en' => 'It was easy to approach the teacher with questions or concerns.',
                'bn' => 'প্রশ্ন বা উদ্বেগ নিয়ে শিক্ষকের কাছে যাওয়া সহজ ছিল ।',
                'created_at' => '2026-01-22 13:35:13',
                'updated_at' => '2026-01-22 13:35:13',
            ],
            9 => [
                'id' => 10,
                'question_no' => 'a)',
                'department_id' => 0,
                'questions_group_id' => 3,
                'en' => 'The class environment was conducive for learning.',
                'bn' => 'শ্রেণীকক্ষের পরিবেশ শিক্ষণের অনুকূল ছিল ।',
                'created_at' => '2026-01-22 13:36:17',
                'updated_at' => '2026-01-22 13:36:17',
            ],
            10 => [
                'id' => 11,
                'question_no' => 'b)',
                'department_id' => 0,
                'questions_group_id' => 3,
                'en' => 'Recommended reading books, references, web-resources etc. were relevant.',
                'bn' => 'শিক্ষণের উপকরণগুলি (যেমন পাঠ পরিকল্পনা, হ্যান্ডআউট ইত্যাদি) প্রাসঙ্গিক ছিল ।',
                'created_at' => '2026-01-22 13:36:52',
                'updated_at' => '2026-01-22 13:36:52',
            ],
            11 => [
                'id' => 12,
                'question_no' => 'a)',
                'department_id' => 0,
                'questions_group_id' => 4,
                'en' => 'Concepts and ideas covered in the course were well articulated and presented.',
                'bn' => 'কোর্সে অন্তর্ভুক্ত ধারণাগুলি ভালভাবে বর্ণিত এবং উপস্থাপিত হয়েছিল ।',
                'created_at' => '2026-01-22 13:38:10',
                'updated_at' => '2026-01-22 13:38:10',
            ],
            12 => [
                'id' => 13,
                'question_no' => 'b)',
                'department_id' => 0,
                'questions_group_id' => 4,
                'en' => 'The objectives of the course were attained.',
                'bn' => 'কোর্সের উদ্দেশ্যগুলি অর্জিত হয়েছিল ।',
                'created_at' => '2026-01-22 13:38:27',
                'updated_at' => '2026-01-22 13:38:27',
            ],
            13 => [
                'id' => 14,
                'question_no' => 'c)',
                'department_id' => 0,
                'questions_group_id' => 4,
                'en' => 'The course assessment methods were fair.',
                'bn' => 'কোর্স মূল্যায়ন পদ্ধতি যথোপযুক্ত ছিল ।',
                'created_at' => '2026-01-22 13:38:27',
                'updated_at' => '2026-01-22 13:38:27',
            ],
        ]);

        // DB::table('questions')->insert(array(
        //     0 =>
        //     array(
        //         'id' => 1,
        //         'question_no' => 'a)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'Learning objectives and outcomes of the course were presented before the students.',
        //         'bn' => 'কোর্সের উদ্দেশ্য ও ফলাফল শিক্ষার্থীদের জানানো হয়েছে ।',
        //         'created_at' => '2022-11-23 13:34:05',
        //         'updated_at' => '2022-12-05 13:20:43',
        //     ),
        //     1 =>
        //     array(
        //         'id' => 2,
        //         'question_no' => 'b)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'The objectives and outcomes of the course were clear and easy to understand.',
        //         'bn' => 'কোর্সের উদ্দেশ্য ও ফলাফল স্পষ্ট এবং সহজবোধ্য ছিল ।',
        //         'created_at' => '2022-11-23 13:43:31',
        //         'updated_at' => '2022-12-05 13:21:29',
        //     ),
        //     2 =>
        //     array(
        //         'id' => 3,
        //         'question_no' => 'c)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'Lesson plans were provided at the commencement of the course.',
        //         'bn' => 'কোর্সের শুরুতে পাঠ-পরিকল্পনা দেওয়া হয়েছিল ।',
        //         'created_at' => '2022-12-05 13:26:35',
        //         'updated_at' => '2022-12-05 13:26:35',
        //     ),
        //     3 =>
        //     array(
        //         'id' => 4,
        //         'question_no' => 'd)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'The course structure helped achieve the learning outcomes (there was a good balance of lectures, tutorials, practical etc.)',
        //         'bn' => 'কোর্স কাঠামোটি শিক্ষণের ফলাফল অর্জনে সহায়ক ছিল (বক্তব্য, টিউটোরিয়াল, ব্যবহারিক ইত্যাদির একটি ভাল ভারসাম্য ছিল)',
        //         'created_at' => '2022-12-05 13:30:21',
        //         'updated_at' => '2022-12-05 13:30:21',
        //     ),
        //     4 =>
        //     array(
        //         'id' => 5,
        //         'question_no' => 'e)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'Satisfaction with the contents of the course.',
        //         'bn' => 'কোর্সের বিষয়বস্তুর ব্যাপারে সন্তুষ্টির মাত্রা ।',
        //         'created_at' => '2022-12-05 13:31:06',
        //         'updated_at' => '2022-12-05 13:31:06',
        //     ),
        //     5 =>
        //     array(
        //         'id' => 6,
        //         'question_no' => 'f)',
        //         'department_id' => 0,
        //         'questions_group_id' => 1,
        //         'en' => 'The workload of the course was manageable.',
        //         'bn' => 'কোর্সের শিক্ষণের চাপ ছিল সহনীয় ।',
        //         'created_at' => '2022-12-05 13:31:34',
        //         'updated_at' => '2022-12-05 13:31:34',
        //     ),
        //     6 =>
        //     array(
        //         'id' => 7,
        //         'question_no' => 'a)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'The teacher followed the sequence of the course contents.',
        //         'bn' => 'শিক্ষক কোর্সের বিষয়বস্তুর ক্রম অনুসরণ করেছেন ।',
        //         'created_at' => '2022-12-05 13:32:12',
        //         'updated_at' => '2022-12-05 13:32:12',
        //     ),
        //     7 =>
        //     array(
        //         'id' => 8,
        //         'question_no' => 'b)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'Teaching methods (lecture, presentation, group discussion, group work etc.) were participatory.',
        //         'bn' => 'শিক্ষাদান পদ্ধতি অংশগ্রহণমূলক (প্রেজেন্টেশন, গ্রুপওয়ার্ক, গ্রুপ ডিসকাশন, বক্তৃতা ইত্যাদি) ছিল ।',
        //         'created_at' => '2022-12-05 13:33:44',
        //         'updated_at' => '2022-12-05 13:33:44',
        //     ),
        //     8 =>
        //     array(
        //         'id' => 9,
        //         'question_no' => 'c)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'I enjoyed the teaching methods of the course teacher.',
        //         'bn' => 'আমি কোর্স শিক্ষকের শিক্ষাদান পদ্ধতি উপভোগ করেছি ।',
        //         'created_at' => '2022-12-05 13:34:06',
        //         'updated_at' => '2022-12-05 13:34:06',
        //     ),
        //     9 =>
        //     array(
        //         'id' => 10,
        //         'question_no' => 'd)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'There were scopes to actively participate in different segments of the course.',
        //         'bn' => 'কোর্সের বিভিন্ন পর্যায়ে সক্রিয়ভাবে অংশগ্রহণের সুযোগ ছিল ।',
        //         'created_at' => '2022-12-05 13:34:25',
        //         'updated_at' => '2022-12-05 13:34:25',
        //     ),
        //     10 =>
        //     array(
        //         'id' => 11,
        //         'question_no' => 'e)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'The course teacher was careful about students’ needs and problems.',
        //         'bn' => 'কোর্স শিক্ষক শিক্ষার্থীদের প্রয়োজন এবং সমস্যা সম্পর্কে সচেতন ছিলেন ।',
        //         'created_at' => '2022-12-05 13:34:42',
        //         'updated_at' => '2022-12-05 13:34:42',
        //     ),
        //     11 =>
        //     array(
        //         'id' => 12,
        //         'question_no' => 'f)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'The course teacher was regular and punctual throughout the course (maintained the class routine, entered and exited the classroom on time).',
        //         'bn' => 'কোর্স শিক্ষক পুরো কোর্সে নিয়মিত এবং সময়নিষ্ঠ ছিলেন (ক্লাসরুটিন অনুযায়ী ক্লাস  নিয়েছেন, সময়মতো ক্লাসে এসেছেন ও ক্লাস শেষ করেছেন) ।',
        //         'created_at' => '2022-12-05 13:35:00',
        //         'updated_at' => '2022-12-05 13:35:00',
        //     ),
        //     12 =>
        //     array(
        //         'id' => 13,
        //         'question_no' => 'g)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'The assessment methods were realistic.',
        //         'bn' => 'কোর্সের মূল্যায়ন পদ্ধতি বাস্তবসম্মত ছিল ।',
        //         'created_at' => '2022-12-05 13:35:13',
        //         'updated_at' => '2022-12-05 13:35:13',
        //     ),
        //     13 =>
        //     array(
        //         'id' => 14,
        //         'question_no' => 'h)',
        //         'department_id' => 0,
        //         'questions_group_id' => 2,
        //         'en' => 'Classes were logically distributed in the routine and held accordingly throughout the semester.',
        //         'bn' => 'এই কোর্স-এর ক্লাসগুলো রুটিনে সেমিস্টারজুড়ে যৌক্তিকভাবে বণ্টিত ও অনুষ্ঠিত হয়েছিল ।',
        //         'created_at' => '2022-12-05 13:35:30',
        //         'updated_at' => '2022-12-05 13:35:30',
        //     ),
        //     14 =>
        //     array(
        //         'id' => 15,
        //         'question_no' => 'a)',
        //         'department_id' => 0,
        //         'questions_group_id' => 3,
        //         'en' => 'The class environment was conducive to learning.',
        //         'bn' => 'শ্রেণিকক্ষের পরিবেশ শিক্ষণের অনুকূল ছিল ।',
        //         'created_at' => '2022-12-05 13:36:17',
        //         'updated_at' => '2022-12-05 13:36:17',
        //     ),
        //     15 =>
        //     array(
        //         'id' => 16,
        //         'question_no' => 'b)',
        //         'department_id' => 0,
        //         'questions_group_id' => 3,
        //         'en' => 'Recommended reading books, references, web resources etc. were relevant and suitable.',
        //         'bn' => 'সুপারিশকৃত পুস্তক, তথ্যসূত্র, ওয়েব-উপকরণ ইত্যাদি প্রাসঙ্গিক এবং উপযুক্ত ছিল ।',
        //         'created_at' => '2022-12-05 13:36:52',
        //         'updated_at' => '2022-12-05 13:36:52',
        //     ),
        //     16 =>
        //     array(
        //         'id' => 17,
        //         'question_no' => 'c)',
        //         'department_id' => 0,
        //         'questions_group_id' => 3,
        //         'en' => 'Learning materials (i.e. lesson plans, handouts etc.) were pertinent and worthwhile.',
        //         'bn' => 'শিক্ষণের উপকরণ (যেমন: পাঠ পরিকল্পনা, হ্যান্ডআউট ইত্যাদি) প্রাসঙ্গিক এবং উপযুক্ত ছিল ।',
        //         'created_at' => '2022-12-05 13:37:04',
        //         'updated_at' => '2022-12-05 13:37:04',
        //     ),
        //     17 =>
        //     array(
        //         'id' => 18,
        //         'question_no' => 'd)',
        //         'department_id' => 0,
        //         'questions_group_id' => 3,
        //         'en' => 'Learning materials/readings were made available to the students.',
        //         'bn' => 'পড়াশোনার জন্য বই-পুস্তক ও অন্যান্য উপকরণ শিক্ষার্থীদের সরবরাহ করা হয়েছিল ।',
        //         'created_at' => '2022-12-05 13:37:21',
        //         'updated_at' => '2022-12-05 13:37:21',
        //     ),
        //     18 =>
        //     array(
        //         'id' => 19,
        //         'question_no' => 'e)',
        //         'department_id' => 0,
        //         'questions_group_id' => 3,
        //         'en' => 'Course contents were interesting, engaging and thought-provoking.',
        //         'bn' => 'কোর্সের বিষয়বস্তু ছিল মজাদার, আকর্ষক এবং চিন্তা-উদ্দীপক ।',
        //         'created_at' => '2022-12-05 13:37:36',
        //         'updated_at' => '2022-12-05 13:37:36',
        //     ),
        //     19 =>
        //     array(
        //         'id' => 20,
        //         'question_no' => 'a)',
        //         'department_id' => 0,
        //         'questions_group_id' => 4,
        //         'en' => 'Concepts and ideas covered in the course were well articulated and presented.',
        //         'bn' => 'কোর্সে অন্তর্ভুক্ত ধারণাগুলি ভালভাবে বর্ণিত এবং উপস্থাপিত হয়েছিল ।',
        //         'created_at' => '2022-12-05 13:38:10',
        //         'updated_at' => '2022-12-05 13:38:10',
        //     ),
        //     20 =>
        //     array(
        //         'id' => 21,
        //         'question_no' => 'b)',
        //         'department_id' => 0,
        //         'questions_group_id' => 4,
        //         'en' => 'The objectives of the course were attained.',
        //         'bn' => 'কোর্সের উদ্দেশ্য অর্জিত হয়েছে ।',
        //         'created_at' => '2022-12-05 13:38:27',
        //         'updated_at' => '2022-12-05 13:38:27',
        //     ),
        // ));
    }
}

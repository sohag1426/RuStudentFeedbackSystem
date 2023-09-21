<?php

namespace App\Console\Commands;

use App\Models\department;
use Illuminate\Console\Command;

class UpdateOrCreateDepartmentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateOrCreateDepartments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Department Seeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $departments = [

            "Accounting and Information Systems",
            "Agronomy and Agricultural Extension",
            "Anthropology",
            "Applied Chemistry & Chemical Engineering",
            "Applied Mathematics",
            "Arabic",
            "Bangla",
            "Banking and Insurance",
            "Biochemistry & Molecular Biology",
            "Botany",
            "Ceramics and Sculpture",
            "Chemistry",
            "Clinical Psychology",
            "Computer Science & Engineering",
            "Crop Science and Technology",
            "Economics",
            "Electrical and Electronic Engineering",
            "English",
            "Faculty of Business Studies",
            "Faculty of Social Science",
            "Finance",
            "Fisheries",
            "Folklore",
            "Genetic Engineering & Biotechnology",
            "Geography & Environmental Studies",
            "Geology & Mining",
            "Graphic Design, Crafts & History of Art",
            "History",
            "Information & Communication Engineering",
            "Information Science & Library Management",
            "International Relations",
            "Islamic History & Culture",
            "Islamic Studies",
            "Law",
            "Law and Land Administration",
            "Management studies",
            "Marketing",
            "Mass Communication and Journalism",
            "Materials Science and Engineering",
            "Mathematics",
            "Microbiology",
            "Music",
            "Painting, Oriental Art & Printmaking",
            "Persian language and literature",
            "Pharmacy",
            "Philosophy",
            "Physical Education and Sports Sciences",
            "Physics",
            "Political Science",
            "Population Science & Human Resource Development",
            "Psychology",
            "Public Administration",
            "Sanskrit",
            "Social Work",
            "Sociology",
            "Statistics",
            "Theatre",
            "Tourism and Hospitality Management",
            "Urdu",
            "Veterinary & Animal Sciences",
            "Zoology",
            "ICT Center"

        ];

        asort($departments);

        foreach ($departments as $department) {

            if (department::where('en_name', $department)->count() == 0) {
                $new_department = new department();
                $new_department->en_name = $department;
                $new_department->save();
            }
        }

        return Command::SUCCESS;
    }
}

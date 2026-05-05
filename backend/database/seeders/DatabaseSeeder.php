<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password123'),
        ]);

        $courses = [
            [
                'title' => 'Laravel API Development',
                'description' => 'Build secure and scalable APIs with Laravel.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6',
                'documentation_url' => 'https://laravel.com/docs',
                'youtube_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY',
                'category' => 'Backend',
                'level' => 'Intermediate',
                'duration_minutes' => 360,
            ],
            [
                'title' => 'React Native for Beginners',
                'description' => 'Create beautiful mobile apps using React Native.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4',
                'documentation_url' => 'https://reactnative.dev/docs/getting-started',
                'youtube_url' => 'https://www.youtube.com/watch?v=0-S5a0eXPoc',
                'category' => 'Mobile',
                'level' => 'Beginner',
                'duration_minutes' => 300,
            ],
            [
                'title' => 'Machine Learning Essentials',
                'description' => 'Understand recommendation and analytics basics.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d',
                'documentation_url' => 'https://scikit-learn.org/stable/user_guide.html',
                'youtube_url' => 'https://www.youtube.com/watch?v=7eh4d6sabA0',
                'category' => 'AI',
                'level' => 'Intermediate',
                'duration_minutes' => 420,
            ],
            [
                'title' => 'Advanced SQL and Query Optimization',
                'description' => 'Master indexing, joins, and database tuning for production systems.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475',
                'documentation_url' => 'https://dev.mysql.com/doc/refman/8.0/en/optimization.html',
                'youtube_url' => 'https://www.youtube.com/watch?v=HXV3zeQKqGY',
                'category' => 'Database',
                'level' => 'Advanced',
                'duration_minutes' => 280,
            ],
            [
                'title' => 'Node.js Microservices Fundamentals',
                'description' => 'Design and deploy microservices with clean APIs and async messaging.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c',
                'documentation_url' => 'https://nodejs.org/en/docs',
                'youtube_url' => 'https://www.youtube.com/watch?v=9zUHg7xjIqQ',
                'category' => 'Backend',
                'level' => 'Intermediate',
                'duration_minutes' => 320,
            ],
            [
                'title' => 'UI/UX Design for Developers',
                'description' => 'Learn practical UI principles, spacing systems, and visual hierarchy.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8',
                'documentation_url' => 'https://developer.apple.com/design/human-interface-guidelines/',
                'youtube_url' => 'https://www.youtube.com/watch?v=c9Wg6Cb_YlU',
                'category' => 'Design',
                'level' => 'Beginner',
                'duration_minutes' => 210,
            ],
            [
                'title' => 'Data Structures in JavaScript',
                'description' => 'Implement arrays, linked lists, trees, and graphs with real examples.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c',
                'documentation_url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'youtube_url' => 'https://www.youtube.com/watch?v=RBSGKlAvoiM',
                'category' => 'Programming',
                'level' => 'Intermediate',
                'duration_minutes' => 260,
            ],
            [
                'title' => 'Python for Data Analysis',
                'description' => 'Analyze datasets and build insights using pandas and visualization tools.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1526378722484-cc5c510fdb96',
                'documentation_url' => 'https://pandas.pydata.org/docs/',
                'youtube_url' => 'https://www.youtube.com/watch?v=vmEHCJofslg',
                'category' => 'Data Science',
                'level' => 'Beginner',
                'duration_minutes' => 300,
            ],
            [
                'title' => 'DevOps CI/CD Pipeline Bootcamp',
                'description' => 'Automate build, test, and deployment workflows for modern applications.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa',
                'documentation_url' => 'https://docs.github.com/en/actions',
                'youtube_url' => 'https://www.youtube.com/watch?v=Ou9j73aWgyE&list=PLdpzxOOAlwvIKMhk8WhzN1pYoJ1YU8Csa',
                'category' => 'DevOps',
                'level' => 'Intermediate',
                'duration_minutes' => 340,
            ],
            [
                'title' => 'Cybersecurity Basics for Developers',
                'description' => 'Protect your applications from common vulnerabilities and attacks.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1510915228340-29c85a43dcfe',
                'documentation_url' => 'https://owasp.org/www-project-top-ten/',
                'youtube_url' => 'https://www.youtube.com/watch?v=inWWhr5tnEA',
                'category' => 'Security',
                'level' => 'Beginner',
                'duration_minutes' => 240,
            ],
            [
                'title' => 'System Design Interview Mastery',
                'description' => 'Understand scalable architecture patterns and communication strategies.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40',
                'documentation_url' => 'https://martinfowler.com/articles/patterns-of-distributed-systems/',
                'youtube_url' => 'https://www.youtube.com/watch?v=UzLMhqg3_Wc',
                'category' => 'Architecture',
                'level' => 'Advanced',
                'duration_minutes' => 410,
            ],
            [
                'title' => 'Next.js Fullstack Development',
                'description' => 'Build production-ready fullstack apps with Next.js App Router and APIs.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97',
                'documentation_url' => 'https://nextjs.org/docs',
                'youtube_url' => 'https://www.youtube.com/watch?v=wm5gMKuwSYk',
                'category' => 'Web',
                'level' => 'Intermediate',
                'duration_minutes' => 360,
            ],
            [
                'title' => 'Docker and Kubernetes Essentials',
                'description' => 'Containerize applications and deploy scalable services on Kubernetes.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9',
                'documentation_url' => 'https://kubernetes.io/docs/home/',
                'youtube_url' => 'https://www.youtube.com/watch?v=Ou9j73aWgyE&list=PLdpzxOOAlwvIKMhk8WhzN1pYoJ1YU8Csa',
                'category' => 'DevOps',
                'level' => 'Intermediate',
                'duration_minutes' => 390,
            ],
            [
                'title' => 'TypeScript Professional Handbook',
                'description' => 'Master advanced typing, generics, and scalable TypeScript architecture.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1518773553398-650c184e0bb3',
                'documentation_url' => 'https://www.typescriptlang.org/docs/',
                'youtube_url' => 'https://www.youtube.com/watch?v=30LWjhZzg50',
                'category' => 'Programming',
                'level' => 'Advanced',
                'duration_minutes' => 295,
            ],
        ];

        $categoryDocHubs = [
            'Backend' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/laravel/',
                'w3schools_url' => 'https://www.w3schools.com/php/',
            ],
            'Mobile' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/react-native-tutorial/',
                'w3schools_url' => 'https://www.w3schools.com/react/',
            ],
            'AI' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/machine-learning/',
                'w3schools_url' => 'https://www.w3schools.com/python/',
            ],
            'Database' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/sql-tutorial/',
                'w3schools_url' => 'https://www.w3schools.com/sql/',
            ],
            'Design' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/ui-ux-design-tutorial/',
                'w3schools_url' => 'https://www.w3schools.com/css/',
            ],
            'Programming' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/data-structures/',
                'w3schools_url' => 'https://www.w3schools.com/js/',
            ],
            'Data Science' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/data-analysis-with-python-course/',
                'w3schools_url' => 'https://www.w3schools.com/ai/ai_sciences.asp',
            ],
            'DevOps' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/devops-tutorial/',
                'w3schools_url' => 'https://www.w3schools.com/docker/',
            ],
            'Security' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/cyber-security-tutorial/',
                'w3schools_url' => 'https://www.w3schools.com/cybersecurity/',
            ],
            'Architecture' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/system-design/',
                'w3schools_url' => 'https://www.w3schools.com/',
            ],
            'Web' => [
                'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/javascript/',
                'w3schools_url' => 'https://www.w3schools.com/html/',
            ],
        ];
        $defaultDocHub = [
            'geeksforgeeks_url' => 'https://www.geeksforgeeks.org/courses/',
            'w3schools_url' => 'https://www.w3schools.com/',
        ];

        $lessonPrimaryVideos = [
            1 => 'https://www.youtube.com/watch?v=MFwJKSM0Yp8',
            2 => 'https://www.youtube.com/watch?v=ImtHd5n9UFM',
            3 => 'https://www.youtube.com/watch?v=hHuG77JITUo',
            4 => 'https://www.youtube.com/watch?v=8aGhZQkoFbQ',
            5 => 'https://www.youtube.com/watch?v=Ke90Tje7VS0',
        ];
        $lessonSupplementaryVideos = [
            1 => 'https://www.youtube.com/watch?v=y6120QOlsfU',
            2 => 'https://www.youtube.com/watch?v=UBOj6rqRUME',
            3 => 'https://www.youtube.com/watch?v=IeTybKL1pM4',
            4 => 'https://www.youtube.com/watch?v=ieTHC78gGTU',
            5 => 'https://www.youtube.com/watch?v=0pThn9npXdE',
        ];

        /** DevOps courses: curated playlist + per-lesson videos (see https://www.youtube.com/watch?v=Ou9j73aWgyE&list=... ). */
        $devOpsLessonPrimaryVideos = [
            1 => 'https://www.youtube.com/watch?v=Ou9j73aWgyE&list=PLdpzxOOAlwvIKMhk8WhzN1pYoJ1YU8Csa',
            2 => 'https://www.youtube.com/watch?v=h7LDnVsNRVI',
            3 => 'https://www.youtube.com/watch?v=scEDHsr3APg',
            4 => 'https://www.youtube.com/watch?v=zpYCV56U5HA',
            5 => 'https://www.youtube.com/watch?v=Xrgk023l4lI',
        ];
        $devOpsLessonSupplementaryVideos = [
            1 => 'https://www.youtube.com/watch?v=h7LDnVsNRVI',
            2 => 'https://www.youtube.com/watch?v=scEDHsr3APg',
            3 => 'https://www.youtube.com/watch?v=zpYCV56U5HA',
            4 => 'https://www.youtube.com/watch?v=Xrgk023l4lI',
            5 => 'https://www.youtube.com/watch?v=Ou9j73aWgyE&list=PLdpzxOOAlwvIKMhk8WhzN1pYoJ1YU8Csa',
        ];
        $lessonGfg = [
            1 => 'https://www.geeksforgeeks.org/array-data-structure/',
            2 => 'https://www.geeksforgeeks.org/string-data-structure/',
            3 => 'https://www.geeksforgeeks.org/sorting-algorithms/',
            4 => 'https://www.geeksforgeeks.org/graph-data-structure-and-algorithms/',
            5 => 'https://www.geeksforgeeks.org/dynamic-programming/',
        ];
        $lessonW3 = [
            1 => 'https://www.w3schools.com/html/html5_intro.asp',
            2 => 'https://www.w3schools.com/css/css_intro.asp',
            3 => 'https://www.w3schools.com/js/js_intro.asp',
            4 => 'https://www.w3schools.com/sql/sql_intro.asp',
            5 => 'https://www.w3schools.com/python/python_intro.asp',
        ];

        foreach ($courses as $courseData) {
            $docHub = $categoryDocHubs[$courseData['category']] ?? $defaultDocHub;
            $course = Course::query()->updateOrCreate(
                ['title' => $courseData['title']],
                array_merge($courseData, $docHub)
            );

            $isDevOps = ($courseData['category'] ?? '') === 'DevOps';

            foreach (range(1, 5) as $position) {
                $primaryVid = $isDevOps ? $devOpsLessonPrimaryVideos[$position] : $lessonPrimaryVideos[$position];
                $suppVid = $isDevOps ? $devOpsLessonSupplementaryVideos[$position] : $lessonSupplementaryVideos[$position];

                Lesson::query()->updateOrCreate([
                    'course_id' => $course->id,
                    'title' => "Lesson {$position}: {$course->title}",
                ], [
                    'content' => "Hands-on content for lecture {$position}. Use the links below for extra videos and reading.",
                    'video_url' => $primaryVid,
                    'notes_url' => $courseData['documentation_url'],
                    'supplementary_video_url' => $suppVid,
                    'geeksforgeeks_url' => $lessonGfg[$position],
                    'w3schools_url' => $lessonW3[$position],
                    'position' => $position,
                    'duration_minutes' => 20 + $position * 5,
                ]);
            }
        }
    }
}

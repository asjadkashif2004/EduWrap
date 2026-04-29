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
                'price' => 49.99,
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
                'price' => 39.99,
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
                'price' => 59.99,
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
                'price' => 44.99,
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
                'price' => 46.99,
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
                'price' => 29.99,
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
                'price' => 34.99,
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
                'price' => 38.99,
                'duration_minutes' => 300,
            ],
            [
                'title' => 'DevOps CI/CD Pipeline Bootcamp',
                'description' => 'Automate build, test, and deployment workflows for modern applications.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa',
                'documentation_url' => 'https://docs.github.com/en/actions',
                'youtube_url' => 'https://www.youtube.com/watch?v=R8_veQiYBjI',
                'category' => 'DevOps',
                'level' => 'Intermediate',
                'price' => 52.99,
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
                'price' => 33.99,
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
                'price' => 64.99,
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
                'price' => 54.99,
                'duration_minutes' => 360,
            ],
            [
                'title' => 'Docker and Kubernetes Essentials',
                'description' => 'Containerize applications and deploy scalable services on Kubernetes.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9',
                'documentation_url' => 'https://kubernetes.io/docs/home/',
                'youtube_url' => 'https://www.youtube.com/watch?v=3c-iBn73dDE',
                'category' => 'DevOps',
                'level' => 'Intermediate',
                'price' => 57.99,
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
                'price' => 42.99,
                'duration_minutes' => 295,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::query()->updateOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );

            foreach (range(1, 5) as $position) {
                Lesson::query()->updateOrCreate([
                    'course_id' => $course->id,
                    'title' => "Lesson {$position}: {$course->title}",
                ], [
                    'content' => "Content for lesson {$position}",
                    'video_url' => $courseData['youtube_url'],
                    'notes_url' => $courseData['documentation_url'],
                    'position' => $position,
                    'duration_minutes' => 20 + $position * 5,
                ]);
            }
        }
    }
}

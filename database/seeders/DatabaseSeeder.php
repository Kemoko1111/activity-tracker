<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed the database with a default admin user, sample activities,
 * and sample log entries for demonstration purposes.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create Admin User ────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@tracker.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Create Member User ───────────────────────────────────────
        $member = User::create([
            'name'     => 'John Doe',
            'email'    => 'john@tracker.com',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);

        $member2 = User::create([
            'name'     => 'Jane Smith',
            'email'    => 'jane@tracker.com',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);

        // ── Create Sample Activities ─────────────────────────────────
        $activities = [
            ['title' => 'Daily SMS count vs logs',          'category' => 'Monitoring',  'description' => 'Compare SMS counts with application logs'],
            ['title' => 'Server health check',              'category' => 'Monitoring',  'description' => 'Check all production servers are responding'],
            ['title' => 'Database backup verification',     'category' => 'Maintenance', 'description' => 'Verify daily database backups completed'],
            ['title' => 'Error log review',                 'category' => 'Monitoring',  'description' => 'Review application error logs for anomalies'],
            ['title' => 'Pending ticket follow-up',         'category' => 'Support',     'description' => 'Follow up on unresolved support tickets'],
            ['title' => 'API response time check',          'category' => 'Monitoring',  'description' => 'Monitor API latency and response times'],
            ['title' => 'Certificate expiry check',         'category' => 'Maintenance', 'description' => 'Check SSL certificates nearing expiry'],
            ['title' => 'Disk space monitoring',            'category' => 'Monitoring',  'description' => 'Monitor disk usage across servers'],
            ['title' => 'Scheduled job verification',       'category' => 'Maintenance', 'description' => 'Verify all cron jobs ran successfully'],
            ['title' => 'Customer complaint review',        'category' => 'Support',     'description' => 'Review and triage new customer complaints'],
        ];

        foreach ($activities as $data) {
            Activity::create(array_merge($data, ['is_active' => true]));
        }

        // ── Create Sample Logs for Today and Yesterday ───────────────
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $allActivities = Activity::all();
        $users = [$admin, $member, $member2];

        foreach ($allActivities as $i => $activity) {
            // Yesterday's logs — all done
            ActivityLog::create([
                'activity_id' => $activity->id,
                'user_id'     => $users[$i % 3]->id,
                'date'        => $yesterday,
                'status'      => 'done',
                'remark'      => 'Completed during morning shift.',
                'created_at'  => Carbon::yesterday()->setTime(9, 30),
            ]);

            // Today's logs — mix of done and pending
            if ($i < 5) {
                ActivityLog::create([
                    'activity_id' => $activity->id,
                    'user_id'     => $users[($i + 1) % 3]->id,
                    'date'        => $today,
                    'status'      => 'done',
                    'remark'      => 'Checked and verified — all clear.',
                    'created_at'  => Carbon::today()->setTime(8, 15),
                ]);
            } else {
                ActivityLog::create([
                    'activity_id' => $activity->id,
                    'user_id'     => $users[($i + 1) % 3]->id,
                    'date'        => $today,
                    'status'      => 'pending',
                    'remark'      => 'Awaiting input from night shift.',
                    'created_at'  => Carbon::today()->setTime(7, 45),
                ]);
            }
        }
    }
}

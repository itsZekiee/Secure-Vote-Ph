<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Election;
use App\Models\Voter;
use App\Mail\ElectionNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendElectionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-election-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated email notifications based on election timeline.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // 1. Pre-Election: 24h reminder
        $reminderElections = Election::where('start_date', '>', $now)
            ->where('start_date', '<=', $now->copy()->addHours(24))
            ->where('status', 'active')
            ->whereNull('reminder_sent_at')
            ->get();

        foreach ($reminderElections as $election) {
            $this->sendToVoters($election, 'reminder');
            $election->update(['reminder_sent_at' => $now]);
            $this->info("Sent reminders for: {$election->title}");
        }

        // 2. Election Open
        $openElections = Election::where('start_date', '<=', $now)
            ->where('end_date', '>', $now)
            ->where('status', 'active')
            ->whereNull('opened_notification_sent_at')
            ->get();

        foreach ($openElections as $election) {
            $this->sendToVoters($election, 'open');
            $election->update(['opened_notification_sent_at' => $now]);
            $this->info("Sent open notifications for: {$election->title}");
        }

        // 3. Election Closed
        $closedElections = Election::where('end_date', '<=', $now)
            ->where('status', 'active')
            ->whereNull('closed_notification_sent_at')
            ->get();

        foreach ($closedElections as $election) {
            $this->sendToVoters($election, 'closed');
            // We can also mark the election as completed here if needed,
            // but the requirement is just to notify.
            $election->update([
                'closed_notification_sent_at' => $now,
                'status' => 'completed'
            ]);
            $this->info("Sent closed notifications for: {$election->title}");
        }
    }

    protected function sendToVoters(Election $election, string $type)
    {
        $election->voters()->chunk(100, function ($voters) use ($election, $type) {
            foreach ($voters as $voter) {
                try {
                    Mail::to($voter->email)->send(new ElectionNotificationMail($election, $type, $voter->name));
                } catch (\Exception $e) {
                    Log::error("Failed to send election notification to {$voter->email}: " . $e->getMessage());
                }
            }
        });
    }
}

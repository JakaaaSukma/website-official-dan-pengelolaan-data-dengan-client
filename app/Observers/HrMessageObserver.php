<?php

namespace App\Observers;

use App\Models\HrMessage;
use App\Models\User;
use App\Mail\HrNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Observer that automatically sends email notifications
 * whenever a new HrMessage is created in the system.
 * 
 * This eliminates the need to add email-sending code to every
 * controller that creates notifications — the Observer pattern
 * ensures ALL notifications trigger emails automatically.
 */
class HrMessageObserver
{
    /**
     * Handle the HrMessage "created" event.
     * 
     * Sends an email notification to the recipient(s):
     * - If to_user_id is set: send to that specific user
     * - If to_user_id is null: send to all Super Admin Recruitment users
     *   (since messages without a specific recipient go to the recruitment team)
     */
    public function created(HrMessage $hrMessage): void
    {
        try {
            // Load relationships needed for email template
            $hrMessage->loadMissing(['fromUser']);

            $recipients = collect();

            if ($hrMessage->to_user_id) {
                // Message has a specific recipient
                $user = User::find($hrMessage->to_user_id);
                if ($user) {
                    $recipients->push($user);
                }
            } else {
                // No specific recipient — send to all Super Admin Recruitment users
                // These are messages from HR Company to the recruitment team
                $recipients = User::where('role', 'super_admin_recruitment')->get();
            }

            if ($recipients->isEmpty()) {
                return;
            }

            foreach ($recipients as $recipient) {
                $emailAddress = $recipient->email;

                if (!$emailAddress) {
                    continue;
                }

                // Don't send email to the sender themselves
                if ($hrMessage->from_user_id === $recipient->id) {
                    continue;
                }

                // Send the notification email (queued for async delivery)
                Mail::to($emailAddress)->send(
                    new HrNotificationMail($hrMessage, $recipient->name)
                );

                Log::channel('activity')->info('HrMessageObserver: Email notification sent', [
                    'message_id' => $hrMessage->id,
                    'to_user_id' => $recipient->id,
                    'to_email' => $emailAddress,
                    'subject' => $hrMessage->subject,
                ]);
            }

        } catch (\Exception $e) {
            // Don't let email failures break the main flow
            Log::error('HrMessageObserver: Failed to send email notification', [
                'message_id' => $hrMessage->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
